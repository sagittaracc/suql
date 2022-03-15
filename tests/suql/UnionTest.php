<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\models\Query2;
use test\suql\models\Query3;
use test\suql\models\Query8;

final class UnionTest extends TestCase
{
    /**
     * Example:
     * 
     * (select table_1.f1, table_1.f2, table_1.f3 from table_1)
     *     union
     * (select table_2.f1, table_2.f2, table_2.f3 from table_2)
     *     union
     * (select table_3.f1, table_3.f2, table_3.f3 from table_3)
     */
    public function testUnion(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q15.php'));

        $query1 = Query1::all()->select(['f1', 'f2', 'f3'])->as('query1');
        $query2 = Query2::all()->select(['f1', 'f2', 'f3'])->as('query2');
        $query3 = Query3::all()->select(['f1', 'f2', 'f3'])->as('query3');

        $actual = $query1->and([$query2, $query3])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * (select table_1.f1, table_1.f2, table_1.f3 from table_1)
     *     union
     * (select table_2.f1, table_2.f2, table_2.f3 from table_2)
     *     union
     * (select table_3.f1, table_3.f2, table_3.f3 from table_3)
     */
    public function testOneModelUnion(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q15.php'));
        $actual = Query8::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}