<?php

use core\SuQLObject;

class RawSuQL extends SuQLObject implements SuQLQueryInterface
{
  protected $driver = 'mysql';

  public function query()
  {
    return 'main';
  }

  public function table()
  {
    return null;
  }

  public function getRawSql()
  {
    return parent::getSQL([$this->query()]);
  }

  public function __toString()
  {
    return $this->getRawSql();
  }

  public static function find()
  {
    $instance = new static();
    $instance->addSelect($instance->query());
    return $instance;
  }

  public function field($raw)
  {
    $this->getQuery($this->query())->addField($this->table(), $raw);
    return $this;
  }
}
