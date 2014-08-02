<?php

namespace OCA\Jot\Db;

use \OCP\AppFramework\Db\Entity;

class Item extends Entity {

    private $created;
    private $modified;
    private $title;
    private $content;
    private $userid;

    public function __construct() {
        $this->addType('created', 'integer');
        $this->addType('modified', 'integer');
        $this->addType('title', 'text');
        $this->addType('content', 'text');
        $this->addType('userid', 'text');
    }
}