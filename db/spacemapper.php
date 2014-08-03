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
        $sql = 'SELECT * FROM `*prefix*jot_spaces` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, array($id));
    }


    public function findAll($userid, $limit=null, $offset=null) {
        $sql = 'SELECT * FROM `*prefix*jot_spaces`' .
            'WHERE `userid` = ?';
        return $this->findEntities($sql, array($userid), $limit, $offset);
    }

    /**
     * Returns the unqiue ID of a space given the userid and name
     * @param string $userid The id of the space owner
     * @param string $name The name of the space in question
     * @return integer The ID of the space
     */
    public function getID($userid, $name) {
        $sql = 'SELECT `name` FROM `*prefix*jot_spaces` ' .
            'WHERE `name` = ? AND `userid` = ?';
        $entity = $this->findEntity($sql, array($name, $userid))
        return $entity ? $entity->getName() : false;
    }

}