<?php
namespace OCA\Jot\AppInfo;

use \OCP\AppFramework\App;

use \OCA\Jot\Controller\PageController;
use \OCA\Jot\Controller\ItemApiController;

use \OCA\Jot\Db\ItemMapper;
use \OCA\Jot\Db\Item;
use \OCA\Jot\Db\SpaceMapper;
use \OCA\Jot\Db\Space;

use \OCA\Jot\BusinessLayer\ItemBusinessLayer;

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
                $c->query('ItemMapper'),
                $c->query('UserId')
            );
        });
        $container->registerService('ItemApiController', function($c) {
            return new ItemApiController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('ItemMapper'),
                $c->query('UserId'),
                $c->query('ItemBusinessLayer'),
                $c->query('Item')
            );
        });



        $container->registerService('UserId', function() {
            return \OCP\User::getUser();
        });
        $container->registerService('Db', function() {
            return new Db();
        });

        $container->registerService('ItemMapper', function($c) {
            return new ItemMapper(
                $c->query('ServerContainer')->getDb(),
                $c->query('SpaceMapper')
            );
        });
        $container->registerService('SpaceMapper', function($c) {
            return new SpaceMapper(
                $c->query('ServerContainer')->getDb()
            );
        });

        $container->registerService('Space', function($c) {
            return new Space();
        });

        $container->registerService('Item', function($c) {
            return new Item();
        });

        /**
         * Business Layer
         */
        $container->registerService('ItemBusinessLayer', function($c) {
            return new ItemBusinessLayer(
                $c->query('Item'),
                $c->query('ItemMapper')    
            );
        });

    }
}