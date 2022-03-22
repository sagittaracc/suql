<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query21 extends ActiveRecord
{
    # hasMany[test\suql\models\Query22(table_22.id)]
    protected $table22_id;

    public function table()
    {
        return 'table_21';
    }

    public function fields()
    {
        return [];
    }
}