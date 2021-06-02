<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;
use suql\syntax\Field;

class ActiveGroups extends ActiveRecord
{
    public function query()
    {
        return 'active_groups';
    }

    public function table()
    {
        return 'users';
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return
            $this->join('user_group')
                ->join('groups')
                    ->select([
                        'name',
                        new Field(['name' => 'count'], [
                            'count',
                        ])
                    ])
                ->group('name');
    }
}