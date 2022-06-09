<?php

namespace test\suql\models\services;

use suql\syntax\entity\SuQLService;
use test\suql\connections\TestConnection;

class TestService extends SuQLService
{
    use TestConnection;
}