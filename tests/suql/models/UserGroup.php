<?php

namespace test\suql\models;

use suql\syntax\Field;
use suql\syntax\Raw;
use test\suql\records\ActiveRecord;

class UserGroup extends ActiveRecord
{
    public function table()
    {
        return 'user_group';
    }

    public function fields()
    {
        return [];
    }
}