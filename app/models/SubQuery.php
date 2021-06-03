<?php

namespace app\models;

use app\records\ActiveRecord;

class SubQuery extends ActiveRecord
{
    public function query()
    {
        return 'sub_query';
    }

    public function table()
    {
        return User::all();
    }

    public function fields()
    {
        return [];
    }
}