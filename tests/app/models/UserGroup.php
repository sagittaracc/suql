<?php

namespace app\models;

use app\records\ActiveRecord;

class UserGroup extends ActiveRecord
{
    public function table()
    {
        return 'user_group';
    }
}