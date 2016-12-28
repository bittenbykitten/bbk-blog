<?php
namespace BBKBlog\Storage;

interface StorageInterface
{
    public function insert(string $table, array $row) : int;

    public function findById(string $table, int $id) : ?array;
}
