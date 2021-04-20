<?php

namespace app\model;

use \PDOSuQL;

class UserDb extends PDOSuQL
{
  protected $dbname = 'test';

  public function getType()
  {
    return 'table';
  }

  public function table()
  {
    return 'users';
  }
}
