<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLService;

class Query27 extends SuQLService
{
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'temp_query';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
