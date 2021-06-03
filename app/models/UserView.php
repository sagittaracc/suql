<?php

namespace app\models;

use app\records\ActiveRecord;

class UserView extends ActiveRecord
{
    public function query()
    {
        return 'user_view';
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
            $this
                ->join('user_group')
                ->join('groups')
                    ->select([
                        'name',
                    ]);
    }
}