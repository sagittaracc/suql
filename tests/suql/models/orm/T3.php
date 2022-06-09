<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;

# [Table(name="ot3")]
class T3 extends TestMySQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $c1;
    public $c2;
}
