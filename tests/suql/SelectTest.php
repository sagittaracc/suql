<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\field\Raw;
use test\suql\models\Query1;
use test\suql\models\Query4;
use test\suql\models\Query5;

final class SelectTest extends TestCase
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
        $actual = Query1::all()->getRawSql();
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
        $actual = Query1::all()->select(['*'])->getRawSql();
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
        $actual = Query1::all()->select(['f1', 'f2'])->getRawSql();
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
        $actual = Query1::all()->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     t.f1,
     *     t.f2,
     *     t.f3
     * from table t
     * 
     */
    public function testSelectWithTableAlias(): void
    {
        $expected = StringHelper::trimSql(require('queries/q5.php'));
        $actual = Query4::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     2 * 2,
     *     'Yuriy' as author
     * 
     */
    public function testSelectRaw(): void
    {
        $expected = StringHelper::trimSql(require('queries/q9.php'));
        $actual = Query5::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     table.f1,
     *     <raw sql expression>
     * from table
     * 
     */
    public function testSelectWithRaw(): void
    {
        $expected = StringHelper::trimSql(require('queries/q10.php'));
        $actual = Query1::all()->select([
            '*',
            Raw::expression("'Yuriy' as author"),
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
