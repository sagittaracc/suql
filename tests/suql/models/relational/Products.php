<?php

namespace test\suql\models\relational;

use suql\syntax\entity\SuQLTable;

# [Table(name="products")]
class Products extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function relations()
    {
        return [
            Categories::class => ['id' => 'category_id'],
        ];
    }
}
