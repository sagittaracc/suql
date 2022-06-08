<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLService;

# [Request(uri="http://jsonplaceholder.typicode.com/posts", method="GET")]
class Query27 extends SuQLService
{
    public function getDb()
    {
        return Container::get('db_test');
    }

    public function relations()
    {
        return [
            Query26::class => ['f1' => 'userId'],
        ];
    }
}
