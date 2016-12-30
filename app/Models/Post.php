<?php
namespace BBKBlog\Models;

class Post extends AbstractModel
{
    private $title;
    private $content;
    private $publishDate;

    public function __construct(string $title, string $content, \DateTimeInterface $publishDate) {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setPublishDate($publishDate);
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setPublishDate(\DateTimeInterface $publishDate)
    {
        $this->publishDate = $publishDate;
    }

    public function getPublishDate()
    {
        return $this->publishDate;
    }
}
