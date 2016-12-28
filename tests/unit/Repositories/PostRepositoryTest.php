<?php
namespace BBKBlog\Repositories;

use PHPUnit\Framework\TestCase;
use BBKBlog\Models\Post;
use BBKBlog\Tests\Helpers\InMemoryStorage;

class PostRepositoryTest extends TestCase
{
    private $post;
    private $repository;
    private $storage;

    /* Tests */

    public function testCanConstruct()
    {
        $this->startWithANewPostRepositoryWithNoStoredPosts();

        $this->assertInstanceOf(PostRepository::class, $this->repository);
    }

    public function testFindByIdReturnsPostForValidId()
    {
        $this->startWithANewPostRepositoryAndStoredPost();

        $this->postExists();

        $this->postDataWasLoadedCorrectly();
    }

    public function testFindByIdReturnsNoPostWhenIdNotFound()
    {
        $this->startWithANewPostRepositoryWithNoStoredPosts();

        $this->postDoesntExist();
    }

    /* Setup */

    private function startWithANewPostRepositoryWithNoStoredPosts()
    {
        $this->storage = new InMemoryStorage();

        $this->post = $this->createMock(Post::class);

        $this->repository = new class($this->storage, $this->post) extends PostRepository {
            public $createPostParams;
            public $mockPost;

            /**
             * Inject mock post
             */
            public function __construct($storage, $mockPost) {
                $this->mockPost = $mockPost;
                parent::__construct($storage);
            }

            /**
             * Override post creation - record method calls and parameters for tests
             */
            protected function createPost($id, $title, $content) {
                $this->createPostParams[] = [
                    'id' => $id,
                    'title' => $title,
                    'content' => $content,
                ];
                return $this->mockPost;
            }
        };
    }

    private function startWithANewPostRepositoryAndStoredPost()
    {
        $this->startWithANewPostRepositoryWithNoStoredPosts();

        $this->storage->insert('posts', $this->getTestPostData());
    }
    
    /* Assertions */

    private function postExistsWithId($id)
    {
        $this->post = $this->repository->findById($id);

        $this->assertInstanceOf(Post::class, $this->post);
    }

    private function postExists() {
        $this->postExistsWithId($this->getTestId());
    }

    private function postDoesntExistWithId($id)
    {
        $this->post = $this->repository->findById($id);

        $this->assertNull($this->post);
    }

    private function postDoesntExist() {
        $this->postDoesntExistWithId($this->getTestId());
    }

    private function postDataWasLoadedCorrectly()
    {
        $this->assertCount(1, $this->repository->createPostParams);

        $this->assertEquals($this->getTestPostData(), $this->repository->createPostParams[0] ?? null);
    }
    
    /* Test Data */

    private function getTestId() {
        return 1;
    }

    private function getTestTitle() {
        return 'Title';
    }

    private function getTestContent() {
        return 'Content';
    }
    
    private function getTestPostData() {
        return [
            'id' => $this->getTestId(),
            'title' => $this->getTestTitle(),
            'content' => $this->getTestContent(),
        ];
    }
}
