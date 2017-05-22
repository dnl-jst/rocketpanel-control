<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hosting;
use AppBundle\Entity\HostingAlias;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/hosting-alias")
 */
class HostingAliasController extends Controller
{

    /**
     * @Route("/{hostname}", name="hostingAlias_getByHostname", defaults={"hostname": null})
     * @Method({"GET"})
     */
    public function getAction($hostname)
    {
    	if (!$hostname) {

		    return new JsonResponse([
			    'code' => 501,
			    'message' => 'parameters missing'
		    ], 501);
	    }

    	/** @var EntityManager $em */
    	$em = $this->getDoctrine()->getManager();

    	/** @var Hosting $hosting */
    	$hosting = $em->getRepository('AppBundle:Hosting')->findOneBy(['hostname' => $hostname]);

    	if (!$hosting) {

		    return new JsonResponse([
			    'code' => 502,
			    'message' => 'hosting for hostname ' . $hostname . ' not found'
		    ], 502);
	    }

		$response = [
			'elements' => []
		];

		foreach ($hosting->getAliases() as $alias) {

			$response['elements'][] = [
				'hostname' => $alias->getHostname(),
				'created'  => $alias->getCreated()->format(\DateTime::W3C)
			];
		}

        return new JsonResponse($response);
    }

	/**
	 * @Route("/", name="hostingAlias_post")
	 * @Method({"POST"})
	 */
    public function postAction(Request $request)
    {
		$hostname = $request->get('hostname');
		$alias = $request->get('alias');

		if (!$hostname || !$alias) {

			return new JsonResponse([
				'code' => 501,
				'message' => 'parameters missing'
			], 501);
		}

	    /** @var EntityManager $em */
	    $em = $this->getDoctrine()->getManager();

	    /** @var Hosting $hosting */
	    $hosting = $em->getRepository('AppBundle:Hosting')->findOneBy(['hostname' => $hostname]);

	    if (!$hosting) {

		    return new JsonResponse([
			    'code' => 502,
			    'message' => 'hosting for hostname ' . $hostname . ' not found'
		    ], 502);
	    }

	    $hostingAlias = $em->getRepository('AppBundle:HostingAlias')->findOneBy(['hostname' => $alias]);

	    if ($hostingAlias) {

		    return new JsonResponse([
			    'code' => 503,
			    'message' => 'hostname already in use'
		    ], 503);
	    }

	    $hostingAlias = new HostingAlias();
	    $hostingAlias->setHosting($hosting);
	    $hostingAlias->setHostname($alias);
	    $hostingAlias->setCreated(new \DateTime());

	    $em->persist($hostingAlias);
	    $em->flush();

	    return new JsonResponse([
	    	'code' => 200,
		    'message' => 'created'
	    ]);
    }

}
