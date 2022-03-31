<?php

namespace test\suql\models\relational;

use test\suql\records\ActiveRecord;

class Users extends ActiveRecord
{
    public function table()
    {
        return 'users';
    }

    public function relations()
    {
        return [
            Products::class => ['id' => 'product_id'],
        ];
    }
}
