<?php

namespace OCA\Jot\Lib;

use OCP\Files\IRootFolder;
use OCA\Jot\Lib\Jot;
use OC\Hooks\BasicEmitter;
use OCA\Jot\Lib\JotService;
use OCP\IConfig;


class Importer extends BasicEmitter{

    protected $rootFolder, $jot, $config, $user, $appName, $jotService;

    public function __construct(
                                $appName,
                                IRootFolder $rootFolder,
                                JotService $jotService,
                                Jot $jot,
                                IConfig $config,
                                $UserId
                            ) {
        $this->jot = $jot;
        $this->rootFolder = $rootFolder;
        $this->jotService = $jotService;
        $this->user = $UserId;
        $this->appName = $appName;
        $this->config = $config;
    }


    /**
     * Performs the actual import from a zip file
     */
    public function import($path, $eventSource) {

        $userFolder = $this->rootFolder->getUserFolder($this->user);
        $zip = $userFolder->get($path);
        $zip = new \OC_Archive_Zip($this->config->getSystemValue('datadirectory').$zip->getPath());
        $files = $zip->getFolder('Takeout/Keep');
//die(var_dump($files));
        $images = [];

        foreach($files as $file) {

            $eventSource->send('progress', 'Handling: '.$file);
            // Split into types
            if(pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                // Handle the jot import
                $eventSource->send('progress', 'Adding Jot: '.basename($file));
                $this->importJotFromHTML($file, $zip->getFile('Takeout/Keep'.$file));
            } else if(pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
                // Save for future image handling
                $eventSource->send('progress', 'Image found: '.$file);
                $images[] = $file;
                // TODO
            }
        }

    }

    public function importJotFromHTML($name,$content) {
        // Parse
        // TODO
        // Import
        $jot = $this->jot;
        $jot->setContent($content);
        $jot->setTitle(current(explode('.', $name)));
        $this->jotService->saveJot($jot);
    }

}
