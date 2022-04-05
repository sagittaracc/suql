<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

class Query10 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'table_10';
    }

    public function commandCastF1ToInt($data)
    {
        return array_map(function($row) {
            $row['f1'] = intval($row['f1']);
            return $row;
        }, $data);
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
