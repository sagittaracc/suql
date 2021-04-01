<?php
use core\SuQLObject;

class SuQL extends SuQLObject
{
  protected $adapter = 'mysql';

  public function query()
  {
    return 'main';
  }

  public function getRawSql()
  {
    return parent::getSQL([$this->query()]);
  }

  public static function find()
  {
    $instance = new static();

    $instance->addSelect($instance->query());
    $instance->getQuery($instance->query())->addFrom($instance->table());

    return $instance;
  }
}
