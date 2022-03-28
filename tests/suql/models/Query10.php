<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query10 extends ActiveRecord
{
    public function table()
    {
        return 'table_10';
    }

    public function commandCastF1ToInt($data)
    {
        return array_map(function($row) {
            $row['f1'] = intval($row['f1']);
            return $row;
        }, $data);
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
