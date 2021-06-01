<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Group extends ActiveRecord
{
    public function table()
    {
        return 'groups';
    }

    public function fields()
    {
        return [
            'name',
        ];
    }
}