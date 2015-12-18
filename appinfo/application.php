<?php
namespace OCA\Jot\AppInfo;

use OCP\AppFramework\App;

use OCA\Jot\Controller\PageController;
use OCA\Jot\Controller\JotApiController;
use OCA\Jot\Lib\JotService;
use OCA\Jot\Lib\Jot;
use OCA\Jot\Lib\ImageService;

class Application extends App {

    public function __construct(array $urlParams=array()){
        parent::__construct('jot', $urlParams);

        $container = $this->getContainer();

        /**
         * Controllers
         */
        $container->registerService('PageController', function($c) {
            return new PageController(
                $c->query('AppName'),
                $c->query('JotService'),
                $c->query('ServerContainer')->getUserSession(),
                $c->query('ImageService')
            );
        });
        $container->registerService('JotApiController', function($c) {
            return new JotApiController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('ServerContainer')->getUserSession()->getUser(),
                $c->query('JotService')
            );
        });

        $container->registerService('JotService', function($c) {
            return new JotService(
                $c->query('AppName'),
                $c->query('ServerContainer')->getRootFolder(),
                $c->query('Jot'),
                $c->query('ServerContainer')->getConfig(),
                $c->query('ServerContainer')->getUserSession()
            );
        });

        $container->registerService('Jot', function($c) {
            return new Jot();
        });

        $container->registerService('ImageService', function($c) {
            return new ImageService(
                $c->query('ServerContainer')->getRootFolder(),
                $c->query('JotSevice'),
                $c->query('ServerContainer')->getConfig()
            );
        });

    }
}
