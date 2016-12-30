<?php
namespace BBKBlog\Repositories;

use BBKBlog\Models\Post;
use BBKBlog\Models\AbstractModel;

class PostRepository extends AbstractRepository
{
    protected $tableName = 'posts';

    protected function createModelFromRow(array $row) : AbstractModel
    {
        return $this->createPost(
            $row['id'] ?? null,
            $row['title'] ?? '',
            $row['content'] ?? '',
            $row['publish_date'] ?? null
        );
    }

    protected function createRowFromModel(AbstractModel $model) : array
    {
        if (!($model instanceof Post)) {
            throw new \Exception('Invalid model type');
        }

        return [
            'id' => $model->getId(),
            'title' => $model->getTitle(),
            'content' => $model->getContent(),
            'publish_date' => $model->getPublishDate(),
        ];
    }

    protected function createPost($id, $title, $content, $publishDate)
    {
        $post = new Post($title, $content, $publishDate);
        $post->setId($id);
        return $post;
    }
}
