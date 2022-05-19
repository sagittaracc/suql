<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="table_1")]
class Query1 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}