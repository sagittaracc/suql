<?php

namespace test\suql\models;

use test\suql\models\tables\TestTable;

# [Table(name="table_10")]
class Query10 extends TestTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function commandSomePostProcess($data)
    {
        foreach ($data as &$row) {
            $row['f1'] = intval($row['f1']);
            $row['f2'] = intval($row['f2']);
        }
        unset($row);

        return $data;
    }
}
