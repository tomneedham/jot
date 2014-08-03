<?php

namespace OCA\Jot\Db;

use \OCP\IDb;
use \OCP\AppFramework\Db\Mapper;

class ItemMapper extends Mapper {

    private $spaceMapper;

    public function __construct(IDb $db, SpaceMapper $spaceMapper) {
        parent::__construct($db, 'jot_items');
        $this->spaceMapper = $spaceMapper;
    }


    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find($id) {
        $sql = 'SELECT * FROM `*prefix*jot_items` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, array($id));
    }


    public function findAll($userid, $space=null, $limit=null, $offset=null) {
        if(is_null($space)) {
            $sql = 'SELECT * FROM `*prefix*jot_items`';
            return $this->findEntities($sql, array(), $limit, $offset);
        } else {
            $sql = 'SELECT * FROM `*prefix*jot_items`' .
            'WHERE `space` = ?';;
            return $this->findEntities($sql, array($this->spaceMapper->getID($userid, $space)), $limit, $offset);
        }
    }

}