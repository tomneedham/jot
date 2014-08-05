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

    }

    /**
     * Fetches a space of items
     * @param string $userid
     * @param integer|null $space The id of the space to load, or null for the homepage, 0 for archived notes
     * @param integer $limit
     * @param integer $offset
     * @return Array Array of \OCA\Jot\Db\Items
     */
    public function fetchView($userid, $space=null, $limit=null, $offset=null) {
        if($space === 0) {
            // Load archived view
        } elseif(is_null($space)) {
            // Load front view (all items, not including archived)
            $sql = 'SELECT * FROM `*PREFIX*jot_items` WHERE `archived` = 0 AND `userid` = ? ORDER BY `created` DESC';
            $items = $this->findEntities($sql, array($userid), $limit, $offset);
            return $items;
        } else {
            // Load a specific space
        }
    }

    public function findAll($userid, $space=null, $limit=null, $offset=null) {
        if(is_null($space)) {
            $sql = 'SELECT * FROM `*PREFIX*jot_items`';
            $items = $this->findEntities($sql, array(), $limit, $offset); 
            return $items;
        } else {
            $sql = 'SELECT * FROM `*PREFIX*jot_items`' .
            'WHERE `space` = ?';;
            return $this->findEntities($sql, array($this->spaceMapper->getID($userid, $space)), $limit, $offset);
        }
    }

}