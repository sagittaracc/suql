<?php

namespace app\model;

use \PDOSuQL;

class ProductDb extends PDOSuQL
{
  protected $dbname = 'store';

  public function query()
  {
    return 'store';
  }

  public function table()
  {
    return 'products';
  }
}
