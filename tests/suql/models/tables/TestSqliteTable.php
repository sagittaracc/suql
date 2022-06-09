<?php

namespace test\suql\models\tables;

use suql\syntax\entity\SuQLTable;
use test\suql\connections\TestSqliteConnection;

class TestSqliteTable extends SuQLTable
{
    use TestSqliteConnection;
}