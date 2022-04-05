<?php

namespace test\suql\models\relational;

use suql\syntax\entity\SuQLTable;

class Products extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'products';
    }

    public function relations()
    {
        return [
            Categories::class => ['id' => 'category_id'],
        ];
    }
}
