<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;
use suql\syntax\field\Raw;

class Query5 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function view()
    {
        return $this->select([
            Raw::expression('2 * 2'),
            Raw::expression("'Yuriy' as author"),
        ]);
    }
}