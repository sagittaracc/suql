<?php

namespace app\model;

class ProductDb extends \PDOSuQLTable
{
  protected $dbname = 'store';

  public function table()
  {
    return 'products';
  }
}
