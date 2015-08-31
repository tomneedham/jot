<?php

namespace OCA\Jot\Lib;

use OCP\Files\IRootFolder;
use OCP\IUser;
use OCP\IConfig;
use OCA\Jot\Lib\Jot;

class JotService {

    protected $rootFolder;
    protected $jot;
    protected $config;
    protected $user;
    protected $appName;

    public function __construct(
                            $appName,
                            IRootFolder $rootFolder,
                            Jot $jot,
                            IConfig $config,
                            IUser $user
                            ) {
        $this->jot = $jot;
        $this->rootFolder = $rootFolder;
        $this->config = $config;
        $this->user = $user;
        $this->appName = $appName;
    }

    /**
     * Retrives the folder for jots for a given user
     */
    public function getJotsFolder(IUser $user) {
        // Get the folder file id
        $id = $this->getJotsFolderID($user);
        $jots = current($this->rootFolder->getUserFolder($user->getUID())->getByID($id));
        if($jots === '') {
            // Delete from database
            $this->config->setUserValue($user->getUID(), $this->appName, 'folderID', '');
            throw new \Exception('Jots folder not found');
        } else {
            return $jots;
        }
    }

    /**
     * Returns the file ID for the jots folder
     * @return is_integer
     */
    public function getJotsFolderID(IUser $user) {
        $id = $this->config->getUserValue($user->getUID(), 'jot', 'folderID');
        if($id === '') {
            // We need to try creating the folder for the first time
            $id = $this->createJotFolder($user);
            // Save the folder id
            $this->config->setUserValue($user->getUID(), $this->appName, 'folderID', $id);
        }
        return $id;
    }

    /**
     * Creates a jot folder in the users filesystem
     * @return integer The file ID
     */
     public function createJotFolder(IUser $user) {
         $files = $this->rootFolder->getUserFolder($user->getUID());
         $name = $files->getNonExistingName('Jots');
         return $files->newFolder($name)->getId();
     }

     /**
      * Retrieves Jot objects for the given user
      * @return array of Jot objects
      */
    public function getUserJots(IUser $user) {
        // Find their jots folder
        $jotsFolder = $this->getJotsFolder($user);
        // Get all text files
        $textFiles = $jotsFolder->searchByMime('text');
        $textFiles = $this->sortFilesByModified($textFiles);
        $out = array();
        foreach($textFiles as $file) {
            // Load a jot object for this
            $jot = $this->loadFromFile($file);
            $out[] = $jot;
        }
        return $out;
    }

    /**
     * Sorts the list of file objects by modified time
     * @param array File objects
     * @return array
     */
    public function sortFilesByModified(array $files) {
        usort($files, function($a, $b) {
            return $a->getMTime() > $b->getMTime();
        });
        return $files;
    }

    /**
     * Loads a Jot instance from its file
     * @param File $file
     * @return Jot
     */
    public function loadFromFile(File $file) {
        $jot = new Jot();
        $jot->setID($file->getId());
        $jot->setTitle(current(explode('.', $file->getName(), 2)));
        $jot->setContent($file->getContent());
        return $jot;
    }

    /**
     * Creates a jot in the filesystem
     * @param Jot
     * @return integer
     */
    public function createJot(Jot $jot, IUser $user) {
        $jots = $this->getJotsFolder($user);
        $name = $jots->getNonExistingName($jot->getTitle());
        $jotFile = $jots->newFile($name.'.txt');
        $jotFile->putContent($jot->getContent());
        return $jotFile->getId();
    }

}

?>
