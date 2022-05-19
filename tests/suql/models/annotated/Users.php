<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="users")]
class Users extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    # hasMany(test\suql\models\Products[products.id])
    protected $product_id;
}