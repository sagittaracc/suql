<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\SqliteBuilder;
use test\suql\models\Query1;

final class SetSqlBuilderTest extends TestCase
{
    public function testSqliteSelect(): void
    {
        $expected = StringHelper::trimSql(require('queries/sqlite/q1.php'));
        $actual = Query1::all()
            ->select([
                'f1' => 'af1',
                'f2' => 'af2'
            ])
            ->setBuilder(SqliteBuilder::class)
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
