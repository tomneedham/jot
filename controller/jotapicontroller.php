<?php
namespace OCA\Jot\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\AppFramework\Http\JSONResponse;
use OCA\Jot\Lib\JotService;
use OCA\Jot\Lib\Jot;
use OCP\IUser;


class JotApiController extends ApiController {

	protected $user;
	protected $jotService;

    public function __construct(
								$appName,
								IRequest $request,
								IUser $user,
								JotService $jotService
								) {
        parent::__construct($appName, $request);
		$this->user = $user;
		$this->jotService = $jotService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
	 * Method to return some JSON with the Jots for a user
     */
    public function getJots() {
		try {
			return new JSONResponse($this->jotService->getUserJots($this->user));
		} catch (Exception $e) {
			// Throw an error here
			die('1');
		}
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
	 * Updates the attributes of a jot
	 * @param integer $id the jot file id
	 * @param string $title
	 * @param string $content
    */
    public function updateItem($id, $title, $content) {
        // TODO validation
		try {
			$jot = $this->jotService->loadFromID($id, $this->user);
			$jot->setTitle($title);
			$jot->setContent($content);
			$this->jotService->saveJot($jot);
			return new JSONResponse($jot->toArray());
		} catch (\Exception $e) {
			die($e->getMessage());
		}
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function newItem($title, $content) {
		$jot = new Jot();
		$jot->setTitle($title);
		$jot->setContent($content);
		try {
			$jot = $this->jotService->saveJot($jot, $this->user);
			return new JSONResponse($jot->toArray());
		} catch (\Exception $e) {
			// Shit went down
			die($e->getMessage());
		}

    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function deleteItem($id) {

    }

}
