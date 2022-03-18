<?php

namespace suql\syntax;

class Column
{
    private $name;
    private $type;
    private $length;
    private $default;

    function __construct($name)
    {
        $this->name = $name;
    }

    public static function create($name)
    {
        return new static($name);
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setDefault($default)
    {
        $this->default = $default;
    }

    public function getDefault()
    {
        return $this->default;
    }
}