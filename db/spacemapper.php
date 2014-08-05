<?php

namespace OCA\Jot\Db;

use \OCP\IDb;
use \OCP\AppFramework\Db\Mapper;

class SpaceMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'jot_spaces');
    }

    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*jot_spaces` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, array($id));
    }


    public function findAll($userid, $limit=null, $offset=null) {
        $sql = 'SELECT * FROM `*PREFIX*jot_spaces`' .
            'WHERE `userid` = ?';
        return $this->findEntities($sql, array($userid), $limit, $offset);
    }

}