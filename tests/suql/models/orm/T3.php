<?php

namespace test\suql\models;

use test\suql\models\tables\TestTable;

# [Table(name="ot3")]
class T3 extends TestTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $c1;
    public $c2;
}
