<?php

namespace test\suql\models\services;

use suql\syntax\entity\SuQLService;
use test\suql\connections\TestMySQLConnection;

class TestService extends SuQLService
{
    use TestMySQLConnection;
}