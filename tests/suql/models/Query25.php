<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLArray;

class Query25 extends SuQLArray
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