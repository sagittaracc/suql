<?php

namespace app\models;

use suql\syntax\SuQL;

class RawQuery extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'raw_query';
    }

    public function table()
    {
        return null;
    }

    public function view()
    {
        return $this->select([
            '2 * 2',
            "'Yuriy' as author",
        ]);
    }
}