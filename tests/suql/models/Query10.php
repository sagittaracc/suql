<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="table_10")]
class Query10 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

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
