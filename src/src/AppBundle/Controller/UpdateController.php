<?php

namespace AppBundle\Controller;

use Docker;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/update")
 */
class UpdateController extends Controller
{
    /**
     * @Route("/", name="update_put")
     */
    public function putAction()
    {
    	/** @var Logger $logger */
	    $logger = $this->get('logger');

    	$systemContainers = [
    		'rocketpanel-mysql',
		    'rocketpanel-control'
	    ];

	    $client = new Docker\DockerClient([
		    'remote_socket' => 'unix:///var/run/docker.sock',
		    'ssl' => false,
	    ]);
	    $docker = new Docker\Docker($client);
	    $containerManager = $docker->getContainerManager();

	    $updatesDone = false;

    	foreach ($systemContainers as $systemContainer) {

			$response = $containerManager->find($systemContainer);

			if (!$response) {

				return new JsonResponse([
					'code' => 501,
					'message' => 'unable to find system container ' . $systemContainer
				], 501);
			}

			$logger->info('checking for updates for image ' . $response->getConfig()->getImage());

			$oldImageId = $response->getImage();

		    $imageManager = $docker->getImageManager();

		    $imageManager->create(
			    null,
			    [
				    'fromImage' => $response->getConfig()->getImage(),
				    'tag'       => 'latest'
			    ]
		    );

		    $updatedImage = $imageManager->find($response->getConfig()->getImage());

		    if ($oldImageId != $updatedImage->getId()) {

		    	$logger->info('docker image ' . $response->getConfig()->getImage() . ' was updated');
			    $updatesDone = true;
		    }
	    }

	    if ($updatesDone) {

			$logger->info('system containers were updated, restarting system containers');

    		foreach ($systemContainers as $systemContainer) {

    			$logger->info('restarting '. $systemContainer);

    			$containerManager->restart($systemContainer, ['t' => 10]);
		    }
	    }

        return new JsonResponse();
    }
}
