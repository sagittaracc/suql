<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="table_7")]
class Query7 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}
