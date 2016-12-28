<?php
namespace BBKBlog\Models;

use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    private $post;

    public function testCanConstruct()
    {
        $this->startWithANewPost();

        $this->assertInstanceOf(Post::class, $this->post);
    }

    public function testIdGetterAndSetter()
    {
        $this->startWithANewPost();

        $this->thePostIdIs(null);

        $this->post->setId($this->getTestId());

        $this->thePostIdIs($this->getTestId());
    }

    public function testTitleGetterAndSetter()
    {
        $this->startWithANewPost();

        $this->thePostTitleIs($this->getTestTitle());

        $this->post->setTitle($this->getDifferentTestTitle());

        $this->thePostTitleIs($this->getDifferentTestTitle());
    }

    public function testContentGetterAndSetter()
    {
        $this->startWithANewPost();

        $this->thePostContentIs($this->getTestContent());

        $this->post->setContent($this->getDifferentTestContent());

        $this->thePostContentIs($this->getDifferentTestContent());
    }

    /* Setup */

    private function startWithANewPost()
    {
        $this->post = new Post($this->getTestTitle(), $this->getTestContent());
    }

    /* Assertions */

    private function thePostIdIs($value)
    {
        $this->assertEquals($value, $this->post->getId());
    }

    private function thePostTitleIs($value)
    {
        $this->assertEquals($value, $this->post->getTitle());
    }

    private function thePostContentIs($value)
    {
        $this->assertEquals($value, $this->post->getContent());
    }
    
    /* Test Data */

    public function getTestId()
    {
        return 5;
    }

    private function getTestTitle()
    {
        return 'Some Title';
    }

    private function getDifferentTestTitle()
    {
        return 'Different Title';
    }

    private function getTestContent()
    {
        return 'Some Content';
    }

    private function getDifferentTestContent()
    {
        return 'Different Content';
    }
}
