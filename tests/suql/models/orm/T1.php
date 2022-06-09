<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="ot1")]
class T1 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

    public $a1;
    public $a2;
}
