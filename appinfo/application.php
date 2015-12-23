<?php

namespace OCA\Jot\AppInfo;

class Application extends \OCP\AppFramework\App {

    /**
     * Define your dependencies in here
     */
    public function __construct(array $urlParams=array()){
        parent::__construct('jot', $urlParams);

        $container = $this->getContainer();

        $container->registerService('OCP\IEventSource', function ($c) {
            return $c->getServer()->createEventSource();
        });
    }

}
