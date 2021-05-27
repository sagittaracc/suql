<?php

namespace resurs\models;

use suql\syntax\SuQL;

class VtValues extends SuQL
{
    protected static $schemeClass = 'resurs\\schema\\AppSchema';
    protected static $sqlDriver = 'mysql';

    public function table()
    {
        return 'c2000vt_values';
    }
}
