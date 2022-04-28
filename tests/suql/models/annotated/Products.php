<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Products extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    # hasOne(test\suql\models\Categories[categories.id])
    protected $category_id;

    public function table()
    {
        return 'products';
    }
}