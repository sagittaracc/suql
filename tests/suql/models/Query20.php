<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query20 extends ActiveRecord
{
    # hasMany[test\suql\models\Query21(table_21.id)]
    protected $table21_id;

    public function table()
    {
        return 'table_20';
    }

    public function fields()
    {
        return [];
    }
}