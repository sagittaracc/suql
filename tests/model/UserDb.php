<?php

namespace app\model;

use \PDOSuQL;

class UserDb extends PDOSuQL
{
  protected $dbname = 'ug';

  public function table()
  {
    return 'users';
  }
}
