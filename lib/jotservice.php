<?php

namespace OCA\Jot\Lib;

use OCP\Files\IRootFolder;
use OCP\IUserSession;
use OCP\IConfig;
use OCA\Jot\Lib\Jot;
use OCP\Files\File;
use OCP\User\User;

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
                            IUserSession $userSession
                            ) {
        $this->jot = $jot;
        $this->rootFolder = $rootFolder;
        $this->config = $config;
        $this->user = $userSession->getUser();
        $this->appName = $appName;
    }

    /**
     * Retrives the folder for jots for a given user
     */
    public function getJotsFolder(User $user) {
        // Get the folder file id
        $id = $this->getJotsFolderID($user);
        $jots = current($this->rootFolder->getUserFolder($user->getUID())->getByID($id));
        if($jots === false) {
            // Delete from database
            $this->config->setUserValue($user->getUID(), $this->appName, 'folderID', '');
            // Recreate folder
            return current($this->rootFolder->getUserFolder($user->getUID())->getByID($this->createJotFolder($user)));
        } else {
            return $jots;
        }
    }

    /**
     * Returns the file ID for the jots folder
     * @return is_integer
     */
    public function getJotsFolderID(User $user) {
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
     public function createJotFolder(User $user) {
         $files = $this->rootFolder->getUserFolder($user->getUID());
         $name = $files->getNonExistingName('Jots');
         return $files->newFolder($name)->getId();
     }

     /**
      * Retrieves Jot objects for the given user
      * @return array of Jot objects
      */
    public function getUserJots(User $user) {
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
        $data = explode("\n", $file->getContent(),2);
        empty($data[0]) ? $jot->setTitle('') : $jot->setTitle($data[0]);
        empty($data[1]) ? $jot->setContent('') : $jot->setContent($data[1]);
        $jot->setMTime($file->getMTime());
        return $jot;
    }

    /**
     * Load a jot from a file id
     * @param integer $id
     * @param IUser $user
     * @return Jot
     */
     public function loadFromID($id, User $user) {
         $file = current($this->getJotsFolder($user)->getByID($id));
         if(!$file instanceof File) {
             throw new \Exception('Cannot find Jot file');
         } else {
             return $this->loadFromFile($file);
         }
     }

    /**
     * Converts the first line of text to a save filename string
     * @param string
     * @return string
     */
    public function createSafeFilename($in) {
        return $in;
    }

    public function saveJot($jot) {
        $jotsFolder = $this->getJotsFolder($this->user);
        // Has an id?
        if(empty($jot->getId())) {
            // Create the file
            $name = $jotsFolder->getNonExistingName($this->createSafeFilename($jot->getTitle()));
            $jotFile = $jotsFolder->newFile($name.'.txt');
            $jot->setId($jotFile->getId());
        } else {
            $jotFile = current($jotsFolder->getById($jot->getId()));
        }
        $jotFile->putContent($jot->getTitle()."\n".$jot->getContent());
        if($jot->getTitle().'.txt' != $jotFile->getName()) {
            // Update the filename
            $name = $jotsFolder->getNonExistingName($this->createSafeFilename($jot->getTitle()));
            $jotFile->move($jotFile->getParent()->getPath().'/'.$name.'.txt');
        }
        return $jot;
    }

}

?>
