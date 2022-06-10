<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLFile;
use test\suql\connections\TestMySQLConnection;

# [File(location="/path/to/file")]
class Query28 extends SuQLFile
{
    use TestMySQLConnection;

    public function getF1()
    {
        // Here we describe how to get the f1 field from the file
        return [1, 2, 3];
    }
}
