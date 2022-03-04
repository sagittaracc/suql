<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query12;

final class TablePlaceholderTest extends TestCase
{
    /**
     * Example:
     * 
     * select * from table
     * 
     */
    public function testSelectAll(): void
    {
        $expected = StringHelper::trimSql(require('queries/q1.php'));
        $actual = Query12::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select table.* from table
     * 
     */
    public function testSelectAllWithTableName(): void
    {
        $expected = StringHelper::trimSql(require('queries/q2.php'));
        $actual = Query12::all()->select(['*'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     table.f1,
     *     table.f2
     * from table
     * 
     */
    public function testSelectFieldList(): void
    {
        $expected = StringHelper::trimSql(require('queries/q3.php'));
        $actual = Query12::all()->select(['f1', 'f2'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     table.f1 as af1,
     *     table.f2 as af2
     * from table
     * 
     */
    public function testSelectUsingAliases(): void
    {
        $expected = StringHelper::trimSql(require('queries/q4.php'));
        $actual = Query12::all()->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
