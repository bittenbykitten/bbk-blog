<?php
namespace BBKBlog\Tests\Helpers;

use BBKBlog\Storage\StorageInterface;

class InMemoryStorage implements StorageInterface
{
    public $tables = [];
    public $id = [];

    public function insert(string $table, array $row) : int
    {
        $id = $this->id[$table] ?? 0;
        $this->id[$table] = ++$id;
        $row['id'] = $id;
        $this->tables[$table][$id] = $row;
        return $id;
    }

    public function findById(string $table, int $id) : ?array
    {
        return $this->tables[$table][$id] ?? null;
    }
}
