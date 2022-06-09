<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;

# [Table(name="ot2")]
class T2 extends TestMySQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $b1;
    public $b2;
}
