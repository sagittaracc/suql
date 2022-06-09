<?php

namespace test\suql\models\tables;

use suql\syntax\entity\SuQLTable;
use test\suql\connections\TestConnection;

class TestTable extends SuQLTable
{
    use TestConnection;
}