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
    	set_time_limit(0);

    	/** @var Logger $logger */
	    $logger = $this->get('logger');

	    $client = new Docker\DockerClient([
		    'remote_socket' => 'unix:///var/run/docker.sock',
		    'ssl' => false,
	    ]);
	    $docker = new Docker\Docker($client);
	    $containerManager = $docker->getContainerManager();

	    # pull latest version of dnljst/rocketpanel-updater
	    $logger->info(shell_exec('docker pull dnljst/rocketpanel-updater:latest'));

	    try {
		    # remove container if exists
		    $containerManager->remove('rocketpanel-updater');
	    } catch (\Exception $e) {}

		$logger->info('spawing update container');

	    try {

		    $containerConfig = new Docker\API\Model\ContainerConfig();
		    $containerConfig->setImage('dnljst/rocketpanel-updater');

		    # add control over docker socket for update process
		    $containerConfig->setVolumes([
		    	'/opt/rocketpanel' => new \stdClass(),
			    '/var/run/docker.sock' => new \stdClass()
		    ]);

		    # set host config with binds
		    $hostConfig = new Docker\API\Model\HostConfig();
		    $hostConfig->setBinds([
			    '/opt/rocketpanel:/opt/rocketpanel',
			    '/var/run/docker.sock:/var/run/docker.sock'
		    ]);

		    $containerConfig->setHostConfig($hostConfig);

		    # create the rocketpanel-updater container
		    $containerManager->create($containerConfig, ['name' => 'rocketpanel-updater']);

		    # start the container
		    $containerManager->start('rocketpanel-updater');

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
