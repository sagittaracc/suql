<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;
use suql\syntax\field\Field;

class Query13 extends ActiveRecord
{
    public function query()
    {
        return 'query_13';
    }

    public function table()
    {
        return 'table_13';
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return $this->select([
            new Field(['f1' => 'mf1'], [
                'max',
            ])
        ]);
    }
}