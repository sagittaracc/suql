<?php

namespace test\suql\models;

use test\suql\models\arrays\TestArray;

class Query24 extends TestArray
{
    public function data()
    {
        /**
         * Допустим храню пароли в массиве а не в базе данных
         */
        return [
            ['id' => 1, 'user' => 'user1', 'pass' => 'pass1'],
            ['id' => 2, 'user' => 'user2', 'pass' => 'pass2'],
        ];
    }

    public function relations()
    {
        return [
            Query25::class => ['user_id' => 'id'],
            Query26::class => ['f1' => 'id'],
        ];
    }
}