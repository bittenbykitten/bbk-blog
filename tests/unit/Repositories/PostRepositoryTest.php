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
    private $insertedPostId;

    /* Tests */

    public function testCanConstruct()
    {
        $this->startWithANewPostRepositoryWithNoStoredPosts();

        $this->assertInstanceOf(PostRepository::class, $this->repository);
    }

    public function testFindByIdReturnsPostForValidId()
    {
        $this->startWithANewPostRepositoryAndStoredPost();

        $this->postWithTestIdExists();

        $this->postDataWasLoadedCorrectly();
    }

    public function testFindByIdReturnsNoPostWhenIdNotFound()
    {
        $this->startWithANewPostRepositoryWithNoStoredPosts();

        $this->postWithTestIdDoesntExist();
    }

    public function testInsertNewPost()
    {
        $this->startWithANewPostRepositoryWithNoStoredPosts();

        $this->insertNewPost();

        $this->insertedPostExists();
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

    private function postWithTestIdExists()
    {
        $this->postExistsWithId($this->getTestId());
    }

    private function insertedPostExists()
    {
        $this->postExistsWithId($this->getLastInsertedPostId());
    }

    private function postDoesntExistWithId($id)
    {
        $this->post = $this->repository->findById($id);

        $this->assertNull($this->post);
    }

    private function postWithTestIdDoesntExist()
    {
        $this->postDoesntExistWithId($this->getTestId());
    }

    private function postDataWasLoadedCorrectly()
    {
        $this->assertCount(1, $this->repository->createPostParams);

        $this->assertEquals($this->getTestPostData(), $this->repository->createPostParams[0] ?? null);
    }

    private function insertNewPost()
    {
        $this->post = $this->getMockPostWithNoId();

        $this->insertedPostId = $this->repository->insert($this->post);

        $this->assertInternalType('int', $this->insertedPostId);
    }

    /* Test Data */

    private function getTestId()
    {
        return 5;
    }

    private function getTestTitle()
    {
        return 'Title';
    }

    private function getTestContent()
    {
        return 'Content';
    }

    private function getTestPostData()
    {
        return [
            'id' => $this->getTestId(),
            'title' => $this->getTestTitle(),
            'content' => $this->getTestContent(),
        ];
    }

    private function getMockPostWithNoId()
    {
        $post = $this->createMock(Post::class);
        $post->method('getId')->willReturn(null);
        $post->method('getTitle')->willReturn($this->getTestTitle());
        $post->method('getContent')->willReturn($this->getTestContent());
        return $post;
    }

    private function getLastInsertedPostId() {
        return $this->insertedPostId;
    }
}
