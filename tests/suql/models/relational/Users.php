<?php

namespace test\suql\models\relational;

use tests\suql\entity\SuQLTable;

class Users extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'users';
    }

    public function relations()
    {
        return [
            Products::class => ['id' => 'product_id'],
        ];
    }
}
