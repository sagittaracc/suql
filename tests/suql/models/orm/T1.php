<?php

namespace test\suql\models;

use test\suql\models\tables\TestTable;

# [Table(name="ot1")]
class T1 extends TestTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $a1;
    public $a2;
}
