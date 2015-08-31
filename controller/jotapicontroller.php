<?php
namespace OCA\Jot\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\AppFramework\Http\JSONResponse;
use OCA\Jot\Lib\JotService;
use OCA\Jot\Lib\Jot;


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
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function newItem($title, $content) {
		$jot = new Jot();
		$jot->setTitle($title);
		$jot->setContent($content);
		try {
			$id = $this->jotService->createJot($jot, $this->user);
			return new JSONResponse($id);
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
