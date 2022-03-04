<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;

final class LimitTest extends TestCase
{
    /**
     * Example:
     * 
     * select * from table limit 3
     * 
     */
    public function testSelectLimit(): void
    {
        $expected = StringHelper::trimSql(require('queries/q12.php'));
        $actual = Query1::all()->limit(3)->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select * from table limit 0, 3
     * 
     */
    public function testSelectOffsetLimit(): void
    {
        $expected = StringHelper::trimSql(require('queries/q13.php'));
        $actual = Query1::all()->offset(3)->limit(3)->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}