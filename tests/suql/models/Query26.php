<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;

# [Table(name="table_26")]
class Query26 extends TestMySQLTable
{
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}