<?php

namespace app\models;

use app\records\ActiveRecord;

class User extends ActiveRecord
{
    public function table()
    {
        return 'users';
    }

    public function fields()
    {
        return [];
    }
}