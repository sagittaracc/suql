<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLFile;
use test\suql\connections\TestMySQLConnection;

# [File(location="tests/suql/files/file1.ini")]
class Query28 extends SuQLFile
{
    use TestMySQLConnection;

    protected function beforeRead($file)
    {
        return parse_ini_string($file->getContent(), true);
    }

    public function getF1($file, $ini)
    {
        $list = [];
        foreach ($ini as $record) {
            $list[] = $record['f1'];
        }
        return $list;
    }

    public function getF2($file, $ini)
    {
        $list = [];
        foreach ($ini as $record) {
            $list[] = $record['f2'];
        }
        return $list;
    }
}
