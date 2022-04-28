<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;
use suql\syntax\field\Field;

class Query13 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_13';
    }

    public function table()
    {
        return 'table_13';
    }

    public function view()
    {
        return $this->select([
            new Field(['f1' => 'mf1'], [
                'max',
            ])
        ]);
    }
}