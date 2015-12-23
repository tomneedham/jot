<?php

namespace OCA\Jot\Lib;

use OCP\Files\IRootFolder;
use OCA\Jot\Lib\Jot;
use OC\Hooks\BasicEmitter;
use OCA\Jot\Lib\JotService;
use OCP\IConfig;

require(__DIR__.'/../vendor/simpledom/simple_html_dom.php');

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
        // Count how many notes we have
        $html = [];
        foreach($files as $file) {
            if(pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $html[] = $file;
            }
        }
        $num = count($html);
        $eventSource->send('progress', 'Found '.$num.' notes to import');
        for($i=0; $i<$num; $i++) {
            $eventSource->send('progress', 'Importing '.($i+1).' of '.$num.': '.$html[$i]);
            $this->importJotFromHTML($html[$i], $zip->getFile('Takeout/Keep'.$html[$i]));
        }

    }

    public function importJotFromHTML($name, $content) {
        // Find just the content
        $doc = \str_get_html($content);
        if(!$doc) {
            return; // TODO throw exception
        }
        $content = $doc->find('div.content', 0)->innerText();

        if(empty($content)) {
            return;
        }

        // Replace the <br> tags
        $content = str_replace('<br>', "\n", $content);

        // Handle lists...
        $list = $doc->find('div.listitem');
        if(count($list) !== 0) {
            // Handle that
            $content = '';
            foreach($list as $item) {
                $content .= ' - '.$item->find('div.text',0)->innerText()."\n";
            }
        }

        $jot = $this->jot;
        $jot->setId(null);
        // Fix char encoding
        $jot->setContent(html_entity_decode($content));
        $name = current(explode('.', $name));
        // Remove slash
        $jot->setTitle(substr($name, 1, strlen($name)));
        $this->jotService->saveJot($jot);
    }

}
