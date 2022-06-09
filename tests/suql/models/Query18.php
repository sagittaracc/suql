<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use test\suql\models\buffers\Buffer;
use test\suql\models\tables\TestSqliteTable;
use test\suql\schema\AppScheme;

# [Table(name="table_1")]
class Query18 extends TestSqliteTable
{
    protected static $bufferClass = Buffer::class;
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;
}
