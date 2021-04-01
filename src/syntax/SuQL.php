<?php
use core\SuQLObject;

class SuQL extends SuQLObject
{
  protected $adapter = 'mysql';

  public static function find()
  {
    $instance = new static();

    

    return $instance;
  }
}
