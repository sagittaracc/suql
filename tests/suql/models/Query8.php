<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query8 extends ActiveRecord
{
    public function table()
    {
        return null;
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return
            $this->union([
                Query1::all()->select(['f1', 'f2', 'f3'])->as('query1'),
                Query2::all()->select(['f1', 'f2', 'f3'])->as('query2'),
                Query3::all()->select(['f1', 'f2', 'f3'])->as('query3'),
            ]);
    }
}