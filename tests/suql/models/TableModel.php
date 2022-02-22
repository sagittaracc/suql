<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class TableModel extends ActiveRecord
{
    public function query()
    {
        return 'tbl_1';
    }

    public function table()
    {
        return 'table_1';
    }

    public function fields()
    {
        return [];
    }
}
