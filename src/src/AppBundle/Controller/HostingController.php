<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hosting;
use AppBundle\Entity\Image;
use Docker\API\Model\ContainerConfig;
use Docker;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/hosting")
 */
class HostingController extends Controller
{

    /**
     * @Route("/", name="hosting_get")
     * @Method({"GET"})
     */
    public function getAction()
    {
    	/** @var EntityManager $em */
    	$em = $this->getDoctrine()->getManager();

    	/** @var Hosting[] $hostings */
    	$hostings = $em->getRepository('AppBundle:Hosting')->findAll();

		$response = [
			'elements' => []
		];

		foreach ($hostings as $hosting) {

			$response['elements'][] = [
				'id'       => $hosting->getId(),
				'hostname' => $hosting->getHostname(),
				'image'    => $hosting->getImage()->getImageName(),
				'created'  => $hosting->getCreated()->format(\DateTime::W3C)
			];
		}

        return new JsonResponse($response);
    }

	/**
	 * @Route("/{id}", name="hosting_getById")
	 * @Method({"GET"})
	 */
    public function getByIdAction($id)
    {
	    /** @var EntityManager $em */
	    $em = $this->getDoctrine()->getManager();

	    /** @var Hosting[] $hostings */
	    $hosting = $em->getRepository('AppBundle:Hosting')->findOneBy(['id' => $id]);

	    $response = [];

	    $response['hosting'] = [
	    	'id'       => $hosting->getId(),
		    'hostname' => $hosting->getHostname(),
		    'image'    => $hosting->getImage()->getImageName(),
		    'created'  => $hosting->getCreated()->format(\DateTime::W3C)
	    ];

	    return new JsonResponse($response);
    }

	/**
	 * @Route("/", name="hosting_post")
	 * @Method({"POST"})
	 */
    public function postAction(Request $request)
    {
    	/** @var Logger $logger */
    	$logger = $this->get('logger');

		$hostname  = basename(trim($request->get('hostname')));
		$imageName = $request->get('imageName');

		if (!$hostname || !$imageName){

			return new JsonResponse([
				'code' => 501,
				'message' => 'parameters missing'
			], 501);
		}

	    if (!preg_match('~^([a-z0-9-]+\.)+[a-z]{2,6}$~', $hostname)) {

		    return new JsonResponse([
			    'code' => 502,
			    'message' => 'invalid hostname'
		    ],502);
	    }

	    /** @var EntityManager $em */
	    $em = $this->getDoctrine()->getManager();

	    if ($em->getRepository('AppBundle:Hosting')->findOneBy(['hostname' => $hostname])) {

		    return new JsonResponse([
			    'code' => 503,
			    'message' => 'hosting with that hostname already exists'
		    ], 503);
	    }

	    /** @var Image $image */
	    $image = $em->getRepository('AppBundle:Image')->findOneBy(['imageName' => $imageName]);

	    if (!$image) {

		    return new JsonResponse([
			    'code' => 504,
			    'message' => 'image not found'
		    ], 504);
	    }

	    $hosting = new Hosting();
	    $hosting->setHostname($hostname);
	    $hosting->setImage($image);
	    $hosting->setCreated(new \DateTime());

	    $em->persist($hosting);

	    $fs = new Filesystem();
	    $fs->mkdir('/opt/rocketpanel/vhosts/' . $hostname . '/httpdocs/');
	    $fs->mkdir('/opt/rocketpanel/vhosts/' . $hostname . '/logs/');

	    $em->flush();

	    $hostingContainerName = 'rocketpanel-hosting-' . $hosting->getId();

	    try {
		    $client = new Docker\DockerClient([
			    'remote_socket' => 'unix:///var/run/docker.sock',
			    'ssl' => false,
		    ]);
		    $docker = new Docker\Docker($client);
		    $containerManager = $docker->getContainerManager();

		    # pull latest version of dnljst/rocketpanel-updater
		    $logger->info(shell_exec('docker pull ' . $hosting->getImage()->getImageName() . ':' . $image->getTagName()));

		    $containerConfig = new ContainerConfig();
		    $containerConfig->setImage($hosting->getImage()->getImageName() . ':' . $image->getTagName());

		    $containerManager->create($containerConfig, ['name' => $hostingContainerName]);
		    $containerManager->start($hostingContainerName);

	    } catch (\Exception $e) {

		    return new JsonResponse([
			    'code' => 500,
			    'message' => $e->getMessage()
		    ], 500);
	    }

	    $this->updateProxyConfiguration();

	    $response = [];

	    $response['hosting'] = [
		    'id'       => $hosting->getId(),
		    'hostname' => $hosting->getHostname(),
		    'image'    => $hosting->getImage()->getImageName(),
		    'created'  => $hosting->getCreated()->format(\DateTime::W3C)
	    ];

	    return new JsonResponse($response);
    }

	/**
	 * @Route("/{id}", name="hosting_delete")
	 * @Method({"DELETE"})
	 */
	public function deleteAction($id)
	{
		$id = (int)$id;

		if (!$id) {

			return new JsonResponse([
				'code' => 501,
				'message' => 'parameters missing'
			], 501);
		}

		/** @var EntityManager $em */
		$em = $this->getDoctrine()->getManager();

		/** @var Hosting $hosting */
		$hosting = $em->getRepository('AppBundle:Hosting')->findOneBy(['id' => $id]);

		if (!$hosting) {

			return new JsonResponse([
				'code' => 502,
				'message' => 'hosting not found'
			], 502);
		}

		$client = new Docker\DockerClient([
			'remote_socket' => 'unix:///var/run/docker.sock',
			'ssl' => false,
		]);
		$docker = new Docker\Docker($client);
		$containerManager = $docker->getContainerManager();

		$hostingContainerName = 'rocketpanel-hosting-' . $hosting->getId();

		$containerManager->stop($hostingContainerName);
		$containerManager->remove($hostingContainerName);

		$fs = new Filesystem();
		$fs->remove('/opt/rocketpanel/vhosts/' . $hosting->getHostname() . '/');

		$em->remove($hosting);
		$em->flush();

		$this->updateProxyConfiguration();

		return new JsonResponse();
	}

	protected function updateProxyConfiguration()
	{
		/** @var EntityManager $em */
		$em = $this->getDoctrine()->getManager();

		/** @var Hosting[] $hostings */
		$hostings = $em->getRepository('AppBundle:Hosting')->findAll();

		$caddyFile = '';

		foreach ($hostings as $hosting) {

			$caddyFile .= '

' . $hosting->getHostname() . '{
	proxy / localhost:8444	
}';

		}

		file_put_contents('/opt/rocketpanel/etc/Caddyfile', $caddyFile);
	}

}
