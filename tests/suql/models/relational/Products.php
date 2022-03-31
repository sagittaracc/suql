<?php

namespace test\suql\models\relational;

use test\suql\records\ActiveRecord;

class Products extends ActiveRecord
{
    public function table()
    {
        return 'products';
    }

    public function relations()
    {
        return [
            Categories::class => ['id' => 'category_id'],
        ];
    }
}
