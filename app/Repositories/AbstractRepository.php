<?php
namespace BBKBlog\Repositories;

use BBKBlog\Storage\StorageInterface;
use BBKBlog\Models\AbstractModel;

abstract class AbstractRepository
{
    protected $tableName;
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function findById(int $id) : ?AbstractModel
    {
        $row = $this->storage->findById($this->tableName, $id);

        return is_null($row) ? null : $this->createModelFromRow($row);
    }

    public function insert(AbstractModel $model) : int
    {
        return $this->storage->insert($this->tableName, $this->createRowFromModel($model));
    }

    abstract protected function createModelFromRow(array $row) : AbstractModel;

    abstract protected function createRowFromModel(AbstractModel $model) : array;
}
