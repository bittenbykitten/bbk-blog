<?php
namespace BBKBlog\Models;

class Post extends AbstractModel
{
    private $title;
    private $content;

    public function __construct(string $title, string $content) {
        $this->setTitle($title);
        $this->setContent($content);
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }
}
