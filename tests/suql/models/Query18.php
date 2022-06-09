<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use test\suql\models\buffers\Buffer;
use test\suql\models\tables\TestSqliteTable;

# [Table(name="table_1")]
class Query18 extends TestSqliteTable
{
    protected static $bufferClass = Buffer::class;
}
