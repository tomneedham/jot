<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\TemplateResponse;


class PageController extends Controller {

	private $itemMapper;
	private $userid;
	protected $appName;

	public function __construct($appName, $request, ItemMapper $itemMapper, $userid) {
		$this->appName = $appName;
		$this->itemMapper = $itemMapper;
		$this->userid = $userid;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function index() {
    	$templateName = 'main';
    	$items = $this->itemMapper->fetchView($this->userid, null, 20);
    	$items = is_null($items) ? array() : $items;
        $parameters = array('items' => $items);
        return new TemplateResponse($this->appName, $templateName, $parameters);
    }

}
