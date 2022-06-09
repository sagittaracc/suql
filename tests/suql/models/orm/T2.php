<?php

namespace test\suql\models;

use test\suql\models\tables\TestTable;

# [Table(name="ot2")]
class T2 extends TestTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $b1;
    public $b2;
}
