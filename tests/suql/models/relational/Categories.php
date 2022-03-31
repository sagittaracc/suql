<?php

namespace test\suql\models\relational;

use test\suql\records\ActiveRecord;

class Categories extends ActiveRecord
{
    public function table()
    {
        return 'categories';
    }
}