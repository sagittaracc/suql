<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;

final class GroupTest extends TestCase
{
    /**
     * Example:
     * 
     * select
     *     table.f1,
     *     count(table.f1) as count
     * from table
     * group table.f1
     */
    public function testSelectGroup(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q7.php'));
        $actual = Query1::all()->select(['f1'])
            ->group('f1')
            ->count(['f1' => 'count'])
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}