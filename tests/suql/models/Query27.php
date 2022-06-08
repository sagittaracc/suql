<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLService;

class Query27 extends SuQLService
{
    protected $uri = 'http://jsonplaceholder.typicode.com/posts';
    protected $method = 'GET';
    protected $body = [];

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
