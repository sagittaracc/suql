<?php

namespace test\suql\models;

use test\suql\models\services\TestService;

# [Request(uri="http://jsonplaceholder.typicode.com/posts", method="GET")]
class Query27 extends TestService
{
    public function relations()
    {
        return [
            Query26::class => ['f1' => 'userId'],
        ];
    }
}
