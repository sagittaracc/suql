<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLFile;
use test\suql\connections\TestMySQLConnection;

# [File(location="tests/suql/files/file1.ini")]
class Query28 extends SuQLFile
{
    use TestMySQLConnection;

    public function getF1($file)
    {
        $list = [];
        foreach (parse_ini_string($file->getContent(), true) as $record) {
            $list[] = $record['f1'];
        }
        return $list;
    }
}
