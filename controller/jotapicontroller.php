<?php
namespace OCA\Jot\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\AppFramework\Http\JSONResponse;
use OCA\Jot\Lib\JotService;
use OCA\Jot\Lib\Jot;
use OCA\Jot\Lib\ImageService;
use OCA\jot\Lib\Importer;
use OCP\IEventSource;

class JotApiController extends ApiController {

	protected $user, $jotService, $imageService, $importer, $eventSource;

    public function __construct(
								$appName,
								IRequest $request,
								$UserId,
								JotService $jotService,
								ImageService $imageService,
								Importer $importer,
								IEventSource $eventSource
								) {
        parent::__construct($appName, $request);
		$this->user = $UserId;
		$this->jotService = $jotService;
		$this->imageService = $imageService;
		$this->importer = $importer;
		$this->eventSource = $eventSource;
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
			$jot = $this->jotService->loadFromID($id);
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
		try {
			$jot = $this->jotService->deleteJot($this->jotService->loadFromID($id));
			return new JSONResponse();
		} catch (\Exception $e) {
			die($e->getMessage());
		}
    }

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function addImage($id) {
	// Check the jot exists
	try {
		$jot = $this->jotService->loadFromID($id);
		// Save the image
		$file = $this->request->getUploadedFile('file');
		return JSONResponse(
			[$this->imageService->storeImageFromTmp($id, $file)->getId()]
		);
	} catch (\Exception $e) {
		die($e->getMessage());
	}
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * Gets the raw image
	 * @param integer $jot The jot file id
	 * @param integer $image The image file id
	 */
	 public function getImage($jot, $image) {
		 $image = $this->imageService->getFromId($image);
		 // TODO return with the image here
	 }

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* Starts the import process given a path to a zip file of notes
	*/
	public function getImport($path) {

		// Trigger the eventSource connection_status
		$this->eventSource->send('progress', 'Preparing to import...');

		$this->importer->import($path, $this->eventSource);

		// Close the connection
		$this->eventSource->send('complete', '');
		$this->eventSource->close();
	 }

}
