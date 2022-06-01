<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;
use suql\syntax\SuQL1;
use Symfony\Component\Yaml\Yaml;

class Query22 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return SuQL1::query('tests/suql/yaml/Query4.yaml', Yaml::class);
    }

    public function fields()
    {
        return [
            'f1',
            'f2',
        ];
    }
}