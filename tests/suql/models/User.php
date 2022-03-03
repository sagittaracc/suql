<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class User extends ActiveRecord
{
    public function table()
    {
        return 'users';
    }

    public function create()
    {
        return [
            'id' => 'integer',
            'login' => 'string',
            'password' => 'string',
            'role' => 'integer',
        ];
    }

    public function fields()
    {
        return [];
    }
}