<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use suql\syntax\field\Field;
use test\suql\schema\AppScheme;

#[Table(name: "table_13")]
class Query13 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function query()
    {
        return 'query_13';
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