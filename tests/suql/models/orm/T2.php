<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="ot2")]
class T2 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

    public $b1;
    public $b2;
}
