<?php
namespace OCA\Jot\Lib;

class Jot {

    protected $title;
    protected $content;
    protected $id;

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

}

?>
