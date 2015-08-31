<?php
namespace OCA\Jot\Lib;

class Jot {

    protected $title;
    protected $content;
    protected $id;
    protected $mTime;

    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getId() {
        return $this->id;
    }

    public function setMTime($mtime) {
        $this->mTime = $mtime;
    }

    public function getMTime() {
        return $this->mTime;
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'mtime' => $this->getMTime()
        );
    }

}

?>
