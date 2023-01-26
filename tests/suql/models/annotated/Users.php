<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

#[Table(name: "users")]
class Users extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    # hasMany(test\suql\models\Products[products.id])
    protected $product_id;
}