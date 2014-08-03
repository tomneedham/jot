<?php

namespace OCA\Jot\Db;

use \OCP\AppFramework\Db\Entity;

class Space extends Entity {

    private $name;
    private $userid;

    public function __construct() {
        $this->addType('name', 'text');
        $this->addType('userid', 'text');
    }
}