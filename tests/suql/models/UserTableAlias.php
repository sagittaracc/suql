<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class UserTableAlias extends ActiveRecord
{
    public function table()
    {
        return '{{u}}';
    }

    public function fields()
    {
        return [];
    }
}