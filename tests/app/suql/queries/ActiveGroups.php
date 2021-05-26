<?php

namespace app\models;

use suql\syntax\Modifier;
use suql\syntax\SuQL;

class ActiveGroups extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'active_groups';
    }

    public function table()
    {
        return 'users';
    }

    public function view()
    {
        return
            $this->join('user_group')
                ->join('groups')
                    ->select([
                        'name',
                        new \suql\syntax\Field(['name' => 'count'], [
                            'count',
                        ])
                    ])
                ->group('name');
    }
}