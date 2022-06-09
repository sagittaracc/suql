<?php

namespace test\suql\models\relational;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

# [Table(name="products")]
class Products extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function relations()
    {
        return [
            Categories::class => ['id' => 'category_id'],
        ];
    }
}
