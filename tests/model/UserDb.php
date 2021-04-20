<?php

namespace app\model;

class UserDb extends \PDOSuQLTable
{
  protected $dbname = 'test';

  public function table()
  {
    return 'users';
  }
}
