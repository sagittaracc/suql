<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

# [Table(name="groups")]
class Groups extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;
}