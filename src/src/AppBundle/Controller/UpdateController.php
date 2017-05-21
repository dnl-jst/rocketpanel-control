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

	    $client = new Docker\DockerClient([
		    'remote_socket' => 'unix:///var/run/docker.sock',
		    'ssl' => false,
	    ]);
	    $docker = new Docker\Docker($client);
	    $containerManager = $docker->getContainerManager();
	    $imageManager = $docker->getImageManager();

	    # update updater image
	    $imageManager->create(
		    null,
		    [
			    'fromImage' => 'dnljst/rocketpanel-updater',
			    'tag'       => 'latest'
		    ]
	    );

	    try {

		    $containerManager->find('rocketpanel-updater');

		    $logger->critical('another rocketpanel-updater container is running');

		    return new JsonResponse([
			    'code' => 501,
			    'message' => 'another rocketpanel-updater container is running'
		    ], 501);

	    } catch (\Exception $e) {}

		$logger->info('spawing update container');

	    try {

		    $containerConfig = new Docker\API\Model\ContainerConfig();
		    $containerConfig->setImage('dnljst/rocketpanel-updater:latest');

		    # add control over docker socket for update process
		    $containerConfig->setVolumes(['/var/run/docker.sock:/var/run/docker.sock']);

		    # create the rocketpanel-updater container
		    $containerManager->create($containerConfig, ['name' => 'rocketpanel-updater', 'rm' => true]);

	    } catch (\Exception $e) {

			$logger->critical($e->getMessage());

		    return new JsonResponse([
			    'code' => 500,
			    'message' => $e->getMessage()
		    ], 500);
	    }


        return new JsonResponse();
    }
}
