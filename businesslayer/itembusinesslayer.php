<?php
/**
 * ownCloud - Jot
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Tom Needham <tom@owncloud.com>
 * @copyright Tom Needham 2014
 */

namespace OCA\Jot\BusinessLayer;

use OCA\Jot\Db\ItemMapper;
use OCA\Jot\Db\Item;

class ItemBusinessLayer {

	private $itemMapper;
	private $item;

	public function __construct(Item $item, ItemMapper $itemMapper) {
		$this->item = $item;
		$this->itemMapper = $itemMapper;
	}

	/** 
	 * Creates a new item in the database
	 * @param string $title
	 * @param string $content
	 * @param string $userid The id of the owner
	 * @param int $space The id of the space for the item
	 */
	public function create($title, $userid, $type='text', $content=null, int $created=null, $modified=null, $space=null) {
		$item = $this->item;
		$item->setContent($content);
		$created = is_null($created) ? time() : $created;
		$item->setCreated($created);
		$modified = is_null($modified) ? time() : $modified;
		$item->setModified($modified);
		$item->setTitle($title);
		$item->setSpaceid($space);
		$item->setUserid($userid);
		return $this->itemMapper->insert($item);
	}

}