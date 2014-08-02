<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\TemplateResponse;

class PageController extends Controller {

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function index() {
    	$templateName = 'main';
        $parameters = array('key' => 'hi');
        return new TemplateResponse($this->appName, $templateName, $parameters);
    }

}