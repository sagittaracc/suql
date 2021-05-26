<?php

namespace app\models;

use suql\syntax\SuQL;

class NoName extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'no_name';
    }

    public function table()
    {
        return ActiveGroups::all();
    }

    public function view()
    {
        return $this->select([
            'name',
            'count',
        ]);
    }
}