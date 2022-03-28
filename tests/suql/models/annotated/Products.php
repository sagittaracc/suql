<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Products extends ActiveRecord
{
    # hasOne[test\suql\models\Categories(categories.id)]
    protected $category_id;

    public function table()
    {
        return 'products';
    }
}