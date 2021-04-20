<?php

namespace app\model;

use \PDOSuQL;

class ProductDb extends PDOSuQL
{
  protected $dbname = 'store';

  public function getType()
  {
    return 'table';
  }

  public function table()
  {
    return 'products';
  }
}
