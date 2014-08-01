<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\Controller;

class PageController extends Controller {

    public function index() {
    	$templateName = 'main';
        $parameters = array('key' => 'hi');
        return new TemplateResponse($this->appName, $templateName, $parameters);
    }

}