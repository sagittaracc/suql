<?php

namespace test\suql\models;

use test\suql\models\arrays\TestArray;

class Query25 extends TestArray
{
    public function data()
    {
        /**
         * Допустим храню пароли в массиве а не в базе данных
         */
        return [
            ['user_id' => 1, 'login' => 'login1'],
            ['user_id' => 2, 'login' => 'login2'],
        ];
    }
}