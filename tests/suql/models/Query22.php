<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;
use suql\syntax\parser\Yaml;
use suql\syntax\SuQL;

class Query22 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return SuQL::query('tests/suql/yaml/Query4.yaml', new Yaml);
    }

    public function fields()
    {
        return [
            'f1',
            'f2',
        ];
    }
}