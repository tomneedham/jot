<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\ApiController;
use \OCP\IRequest;
use \OCP\AppFramework\Http\JSONResponse;


class ItemApiController extends ApiController {

	private $itemMapper;
	private $userID;

    public function __construct($appName, IRequest $request, ItemMapper $itemMapper, $userID) {
        parent::__construct($appName, $request);
        $this->itemMapper = $itemMapper;
        $this->userID = $userID;
    }

    /**
     * @CORS
     */
    public function getItems($space=null, $limit=null, $offset=null) {
    	$items = $this->itemMapper->findAll($this->userID, $space, $limit, $offset);
    	return new JSONResponse($items);
    }

}