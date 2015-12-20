<?php

namespace OCA\Jot\Lib;

use OCP\Files\IRootFolder;
use OCA\Jot\Lib\JotService;
use OCA\Jot\Lib\Jot;
use OCA\Jot\Lib\Image;
use OCP\IConfig;

class ImageService {

    protected $appName, $rootFolder, $jotService, $appConfig;

    public function __construct($appName, IRootFolder $rootFolder, JotService $jotService, IConfig $appConfig) {
        $this->rootFolder = $rootFolder;
        $this->jotService = $jotService;
        $this->appConfig = $appConfig;
        $this->appName = $appName;
    }

    public function getImageFolder($user) {
        $id = $this->getImageFolderID($user);
        $imageFolder = current($this->rootFolder->getUserFolder($user)->getById($id));
        if($imageFolder === false) {
            // Doesnt exist, or not there
            $jotsFolder = $this->jotService->getJotsFolder($user);
            $name = $jotsFolder->getNonExistingName('images');
            $imageFolder = $jotsFolder->newFolder($name);
            // Set in db
            $this->appConfig->setUserValue($user, $this->appName, 'imageID', $imageFolder->getId());
        }
        return $imageFolder;
    }

    public function getImageFolderID($user) {
        return $this->appConfig->getUserValue($user, $this->appName, 'imageID');
    }

    /**
     * Stores an image for a given jot
     * @param Jot $jot
     * @param
     * @param IUser @user
     * @return Image
     */
    public function storeImageFromTmp(Jot $jot, $file, $user) {
        $imagesFolder = $this->getImageFolder($user);
        $name = $imagesFolder->getNonExistingName($jot->getId().'.jpg');
        $file = $imagesFolder->newFile($name);
        $file->putContent(fopen($file['tmp_name'], 'rb'));
        return $file;
    }

    /**
     * Returns an array of file objects for a given Jot
     * @param Jot $jot
     * @param IUser $user
     * @return File{]
     */
    public function getImagesForJot(Jot $jot, $user) {
        $imageFolder = $this->getImageFolder($user);
        $files = $imageFolder->getDirectoryListing();
        $return = array();
        foreach($files as $file) {
            if(strpos($jot->getId(), $file->getName()) != 0) {
                $out[] = $file;
            }
        }
        return $out;
    }



}
