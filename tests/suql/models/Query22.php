<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use suql\syntax\parser\Yaml;
use suql\syntax\SuQL;
use test\suql\schema\AppScheme;

class Query22 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

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