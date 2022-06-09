<?php

namespace test\suql\models\arrays;

use suql\syntax\entity\SuQLArray;
use test\suql\connections\TestMySQLConnection;

class TestArray extends SuQLArray
{
    use TestMySQLConnection;
}