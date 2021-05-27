<?php

namespace app\records;

use suql\syntax\SuQL;

abstract class ActiveRecord extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function getDb()
    {
        return null;
    }
}