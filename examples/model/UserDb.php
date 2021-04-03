<?php

namespace app\model;

use \PDOSuQL;

class UserDb extends PDOSuQL
{
  protected $dbname = 'test';

  public function query()
  {
    return 'user';
  }

  public function table()
  {
    return 'users';
  }
}
