<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query22;
use test\suql\models\Query6;

final class NestedQueryTest extends TestCase
{
    /**
     * Example:
     * 
     * select
     *     t.f1,
     *     t.f2
     * from (
     *     select
     *         table.f1,
     *         table.f2,
     *         table.f3
     *     from table
     * ) t
     */
    public function testNestedQuery(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q14.php'));
        $actual = Query6::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testNestedQueryOverYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q38.php'));
        $actual = Query22::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}