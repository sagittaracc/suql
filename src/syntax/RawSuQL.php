<?php

use core\SuQLObject;
use core\SuQLScheme;

class RawSuQL extends SuQLObject implements SuQLQueryInterface
{
  protected $driver = 'mysql';

  function __construct()
  {
    $scheme = new SuQLScheme();
    parent::__construct($scheme);
  }

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
    return $instance;
  }

  public function field($raw)
  {
    $this->getQuery($this->query())->addField(null, $raw);
    return $this;
  }
}
