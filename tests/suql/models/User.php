<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class User extends ActiveRecord
{
    public function table()
    {
        return 'users';
    }
}