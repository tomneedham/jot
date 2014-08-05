<?php

namespace OCA\Jot\Db;

use \OCP\AppFramework\Db\Entity;

class Item extends Entity {

    protected $created;
    protected $modified;
    protected $title;
    protected $content;
    protected $userid;
    protected $spaceid;
    protected $archived;
    protected $type;

    public function __construct() {
        $this->addType('created', 'integer');
        $this->addType('modified', 'integer');
        $this->addType('spaceid', 'integer');
        $this->addType('archived', 'boolean');
    }

    public function toAPI() {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'created' => $this->getCreated(),
            'modified' => $this->getModified(),
            'space' => $this->getSpaceid(),
            'type' => $this->getType(),
            'archived' => $this->getArchived()
        );
    }
}