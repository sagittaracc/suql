<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\db\Container;
use suql\syntax\entity\SuQLTable;
use test\suql\models\buffers\Buffer;
use test\suql\schema\AppScheme;

# [Table(name="table_1")]
class Query18 extends SuQLTable
{
    protected static $bufferClass = Buffer::class;
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function getDb()
    {
        return Container::get('db-sqlite');
    }
}
