<?php
namespace BBKBlog\Tests\Helpers;

use BBKBlog\Storage\StorageInterface;

class InMemoryStorage implements StorageInterface
{
    public $tables = [];
    public $id = [];

    public function insert(string $table, array $row) : int
    {
        $last_insert_id = ($this->id[$table] ?? 0);

        if (isset($row['id'])) {
            $id = $row['id'];
        } else {
            $id = $last_insert_id + 1;
        }

        if (array_key_exists($id, $this->tables)) { throw new \Exception('Insert failed: id already exists'); };

        $this->id[$table] = $id;
        $row['id'] = $id;

        $this->tables[$table][$id] = $row;
        return $id;
    }

    public function findById(string $table, int $id) : ?array
    {
        return $this->tables[$table][$id] ?? null;
    }
}
