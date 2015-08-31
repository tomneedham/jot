<?php
namespace OCA\Jot\AppInfo;

use OCP\AppFramework\App;

use OCA\Jot\Controller\PageController;
use OCA\Jot\Controller\JotApiController;
use OCA\Jot\Lib\JotService;
use OCA\Jot\Lib\Jot;

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
                $c->query('Request'),
                $c->query('JotService'),
                $c->getServer()->getUserSession()->getUser()
            );
        });
        $container->registerService('JotApiController', function($c) {
            return new JotApiController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->getServer()->getUserSession()->getUser(),
                $c->query('JotService')
            );
        });

        $container->registerService('JotService', function($c) {
            return new JotService(
                $c->query('AppName'),
                $c->getServer()->getRootFolder(),
                $c->query('Jot'),
                $c->getServer()->getConfig(),
                $c->getServer()->getUserSession()->getUser()
            );
        });

        $container->registerService('Jot', function($c) {
            return new Jot();
        });

    }
}
