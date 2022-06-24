<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query30;

final class MacrosTest extends TestCase
{
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
        $expected = StringHelper::trimSql(require('queries/mysql/q42.php'));
        $actual = Query30::all()->macros1('param1')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
