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
            $row['content'] ?? ''
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
        ];
    }

    protected function createPost($id, $title, $content)
    {
        $post = new Post($title, $content);
        $post->setId($id);
        return $post;
    }
}
