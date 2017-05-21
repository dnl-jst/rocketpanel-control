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
    public function indexAction()
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
				'hostname' => $hosting->getHostname(),
				'image'    => $hosting->getImage()->getImageName(),
				'created'  => $hosting->getCreated()->format(\DateTime::W3C)
			];
		}

        return new JsonResponse($response);
    }

}
