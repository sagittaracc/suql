<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\models\Query13;
use test\suql\models\Query14;
use test\suql\models\Query15;
use test\suql\models\Query2;

final class AdvancedQueryTest extends TestCase
{
    /**
     * Example:
     * 
     * select
     *     table_3.f1,
     *     count(table_3.f1) as count
     * from table_1
     * inner join table_2 on table_1.id = table_2.id
     * inner join table_3 on table_2.id = table_3.id
     * group by table_3.f1
     * 
     */
    public function testSelectGroupWithJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q22.php'));
        $actual = Query1::all()
            ->getQuery2()
            ->getQuery3()
                ->select(['f1'])
            ->group('f1')
            ->count(['f1' => 'count'])
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table_1
     * inner join (
     *     select
     *         max(table_2.f1) as mf1
     *     from table_2
     * ) t2 on table_1.id = table_2.id
     * 
     */
    public function testJoinWithSubQuery(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q23.php'));
        $actual1 = Query1::all()->join(Query13::all())->getRawSql();
        $actual2 = Query1::all()->getQuery13()->getRawSql();
        $this->assertEquals($expected, $actual1);
        $this->assertEquals($expected, $actual2);
    }
    /**
     * Example:
     * 
     * select
     *     table_1.f1
     * from table_1
     * inner join table_2 on table_1.id = table_2.id
     * inner join (
     *     select
     *         table.*
     *     from table
     *     limit 1
     * ) table_3 on table_2.id = table_3.id
     * 
     */
    public function testSmartJoinWithView(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q26.php'));

        $actual1 =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->join(Query15::all(), 'inner', 'smart')
                ->getRawSql();

        $actual2 =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->getQuery15([
                    'algorithm' => 'smart',
                ])
                ->getRawSql();

        
        $this->assertEquals($expected, $actual1);
        $this->assertEquals($expected, $actual2);
    }
    /**
     * Example:
     * 
     * select * from (
     *     (select table_1.f1, table_1.f2, table_1.f3 from table_1)
     *         union
     *     (select table_2.f1, table_2.f2, table_2.f3 from table_2)
     * ) t
     * 
     */
    public function testNestedUnion(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q24.php'));
        $actual = Query14::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     table_1.f1 as af1,
     *     table_1.f2
     * from table_1
     * where table_1.f1 not in (
     *     select distinct
     *         table_2.f1
     *     from table_2
     * )
     * 
     */
    public function testSelectWhereNestedQuery(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q25.php'));
        $actual = Query1::all()->select([
            'f1' => 'af1',
            'f2',
        ])->where('table_1.f1 not in ?', [Query2::all()->distinct(['f1'])])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}