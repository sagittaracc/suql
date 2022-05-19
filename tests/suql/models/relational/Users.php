<?php

namespace test\suql\models\relational;

use suql\syntax\entity\SuQLTable;

# [Table(name="users")]
class Users extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function relations()
    {
        return [
            Products::class => ['id' => 'product_id'],
        ];
    }
}
