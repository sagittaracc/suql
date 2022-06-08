<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLService;

class Query27 extends SuQLService
{
    public function getDb()
    {
        return Container::get('db_test');
    }
}
