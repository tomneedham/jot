<?php
namespace OCA\Jot\AppInfo;

use \OCP\AppFramework\App;

use \OCA\Jot\Controller\PageController;


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
                $c->query('Request')
            );
        });
        $container->registerService('ItemApiController', function($c) {
            return new PageController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('UserId')
            );
        });

        /**
         * Database Layer
         */
        $container->registerService('ItemMapper', function($c) {
            return new ItemMapper($c->query('ServerContainer')->getDb());
        });
        $container->registerService('SpaceMapper', function($c) {
            return new SpaceMapper($c->query('ServerContainer')->getDb());
        });
    }
}