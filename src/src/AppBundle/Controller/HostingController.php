<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hosting;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

}
