<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

#[Table(name: "ot3")]
class T3 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

    public $c1;
    public $c2;
}
