<?php

namespace test\suql\models\tables;

use suql\syntax\entity\SuQLTable;
use test\suql\connections\TestMySQLConnection;

class TestMySQLTable extends SuQLTable
{
    use TestMySQLConnection;
}