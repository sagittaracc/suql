<?php

use core\SuQLObject;
use core\SuQLScheme;
use suql\builder\SQLDriver;

class RawSuQL extends SuQLObject implements SuQLQueryInterface
{
  protected $dbms = 'mysql';

  function __construct()
  {
    $scheme = new SuQLScheme();
    $driver = new SQLDriver($this->dbms);
    parent::__construct($scheme, $driver);
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
