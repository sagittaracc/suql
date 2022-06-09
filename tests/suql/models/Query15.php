<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

# [Table(name="table_15")]
class Query15 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function query()
    {
        return 'query_15';
    }

    public function view()
    {
        return
            $this
                ->select(['*'])
                ->limit(1);
    }
}