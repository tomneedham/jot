<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\TemplateResponse;
use OCP\IUser;
use OCA\Jot\Lib\JotService;



class PageController extends Controller {

	private $jotService;
	private $user;
	protected $appName;

	public function __construct($appName, $request, JotService $jotService, IUser $user) {
		$this->appName = $appName;
		$this->jotService = $jotService;
		$this->user = $user;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function index() {
    	try {
			$jots = $this->jotService->getUserJots($this->user);
		} catch (\Exception $e) {
			// Didn't work
		die($e->getMessage());
		}
        $parameters = array(
        	'jots' => $jots,
        	'appversion' => \OCP\App::getAppVersion('jot')
        );
        return new TemplateResponse($this->appName, 'main', $parameters);
    }

}
