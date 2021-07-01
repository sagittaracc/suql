<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;
use suql\db\Container;

class TableName extends ActiveRecord
{
    public function table()
    {
        return 'table_name';
    }

    public function fields()
    {
        return [];
    }

    public function commandToInt($data)
    {
        return array_map(function($row) {
            $row['field'] = intval($row['field']);
            return $row;
        }, $data);
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
