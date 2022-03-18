<?php

namespace suql\syntax;

class Model
{
    private $name;
    private $columnList;
    private $currentColumn;

    function __construct($name)
    {
        $this->name = $name;
        $this->columnList = [];
        $this->currentColumn = null;
    }

    public static function create($name)
    {
        return new static($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function column($column)
    {
        $this->columnList[$column] = Column::create($column);
        $this->currentColumn = $column;
        return $this;
    }

    public function getColumns()
    {
        return $this->columnList;
    }

    public function getCurrentColumn()
    {
        return $this->currentColumn;
    }

    public function setType($type)
    {
        $this->columnList[$this->currentColumn]->setType($type);
        return $this;
    }

    public function getTypeByColumnName($column)
    {
        return $this->columnList[$column]->getType();
    }

    public function setLength($length)
    {
        $this->columnList[$this->currentColumn]->setLength($length);
        return $this;
    }

    public function getLengthByColumnName($column)
    {
        return $this->columnList[$column]->getLength();
    }

    public function setDefault($default)
    {
        $this->columnList[$this->currentColumn]->setDefault($default);
        return $this;
    }

    public function getDefaultByColumnName($column)
    {
        return $this->columnList[$column]->getDefault();
    }
}