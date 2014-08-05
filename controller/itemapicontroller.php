<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\Db;

use \OCP\AppFramework\ApiController;
use \OCP\IRequest;
use \OCP\AppFramework\Http\JSONResponse;
use \OCA\Jot\Db\Item;
use \OCA\Jot\Db\ItemMapper;

use \OCA\Jot\BusinessLayer\ItemBusinessLayer;

class ItemApiController extends ApiController {

	private $itemMapper;
	private $userid;
    private $bl;
    private $item;

    public function __construct($appName, IRequest $request, ItemMapper $itemMapper, $userid, ItemBusinessLayer $bl, Item $item) {
        parent::__construct($appName, $request);
        $this->itemMapper = $itemMapper;
        $this->userid = $userid;
        $this->bl = $bl;
        $this->item = $item;
    }

    /**
     * @CORS
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getItems($space=null, $limit=100, $offset=null) {
        $items = $this->itemMapper->fetchSpace($this->userid, $space, $limit, $offset);
        $result = array();
        foreach($items as $item) {
            $result[] = $item->toAPI();
        }
        return new JSONResponse(array('success' => true, 'items' => $result));
    }

    /**
     * @CORS
     * @NoAdminRequired
     * @NoCSRFRequired
    */
    public function updateItem($id, $title, $content, int $archived, int $modified) {
        $item = $this->item;
        $item->setId($id);
        if(!is_null($title)) {
            $item->setTitle($title);
        }
        if(!is_null($content)) {
            $item->setContent($content);
        }
        if(!is_null($modified)) {
            $item->setContent($modified);
        }
        if(!is_null($archived)) {
            $item->setArchived($archived);
        }
        $item->setModified(time());
        try {
            $this->itemMapper->update($item);
            return new JSONResponse(array('success' => true));
        } catch (InvalidArgumentException $e) {
            return new JSONResonse(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * @CORS
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function newItem($space=null, $title, $content) {
        if(empty($title)) {
            return new JSONResponse(array('success' => false, 'message' => 'Please provide a title'));
        }
        // Create a new Item entity
        $item = $this->bl->create(
            $title,
            $this->userid,
            $content,
            'text',
            time(),
            time(),
            $space
        );

        return new JSONResponse(
            array(
                'success' => true,
                'id' => $item->getId()
            )
        );
    }

    /**
     * @CORS
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function deleteItem($id) {

    }

}