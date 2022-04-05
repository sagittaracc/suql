<?php

namespace test\suql\models;

use tests\suql\entity\SuQLTable;

class Users extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    # hasMany[test\suql\models\Products(products.id)]
    protected $product_id;

    public function table()
    {
        return 'users';
    }
}