<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLArray;

class Query25 extends SuQLArray
{
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

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

    public function getDb()
    {
        return Container::get('db_test');
    }
}