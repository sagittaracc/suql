<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Users extends ActiveRecord
{
    # hasMany[test\suql\models\Products(products.id)]
    protected $product_id;

    public function table()
    {
        return 'users';
    }

    public function fields()
    {
        return [];
    }
}