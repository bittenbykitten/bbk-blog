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

    abstract protected function createModelFromRow(array $row) : AbstractModel;
}
