<?php

namespace AppBundle\Controller;

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
        return new JsonResponse('hosting');
    }
}
