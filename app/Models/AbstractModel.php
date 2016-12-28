<?php
namespace BBKBlog\Models;

abstract class AbstractModel
{
    private $id;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}
