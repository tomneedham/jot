<?php
namespace OCA\Jot\Controller;

use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCA\Jot\Lib\JotService;
use \OCA\Jot\Lib\ImageService;
use \OCP\IUserSession;

class PageController extends Controller {

	private $jotService;
	private $user;
	protected $appName;
	protected $imageService;

	public function __construct($appName, JotService $jotService, IUserSession $userSession, ImageService $imageService) {
		$this->appName = $appName;
		$this->jotService = $jotService;
		$this->user = $userSession->getUser();
		$this->imageService = $imageService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function index() {
    	try {
			$jots = $this->jotService->getUserJots($this->user);
			foreach($jots as $jot) {
				$images = $this->imageService->getImagesForJot($jot, $this->user);
				foreach($images as $image) {
					$jot->setImage($image->getId(), 'http://owncloud.local/index.php/apps/jot/api/1.0/jots/'.$jot->getId().'/images/'.$image->getId().'.jpg');
				}
			}
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
