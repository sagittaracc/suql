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
        return array_column($ini, 'f1');
    }

    public function getF2($file, $ini)
    {
        return array_column($ini, 'f2');
    }

    public function relations()
    {
        return [
            Query26::class => ['f1' => 'f1'],
        ];
    }
}
