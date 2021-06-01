<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class UserGroup extends ActiveRecord
{
    public function table()
    {
        return 'user_group';
    }

    public function fields()
    {
        return [
            'group_id',
        ];
    }
}