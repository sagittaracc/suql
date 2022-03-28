<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Categories extends ActiveRecord
{
    public function table()
    {
        return 'categories';
    }
}