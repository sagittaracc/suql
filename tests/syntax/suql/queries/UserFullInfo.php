<?php

namespace app\models;

use suql\syntax\SuQL;

class UserFullInfo extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'user_full_info';
    }

    public function table()
    {
        return 'users';
    }

    public function view()
    {
        return
            $this->select([
                'id',
            ])
            ->join('user_group')
            ->join('groups')
                ->select([
                    'id' => 'gid',
                    'name' => 'gname',
                ]);
    }
}