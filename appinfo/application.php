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
    }
}