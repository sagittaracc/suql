<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\schema\NamedRel1;
use test\suql\schema\NamedRel2;

final class JoinTest extends TestCase
{
    /**
     * Example:
     * 
     * select
     *     *
     * from table_1
     * join table_2 on table_1.id = table_2.id
     * join table_3 on table_2.id = table_3.id
     * 
     */
    public function testSimpleJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->join('table_2')
                ->join('table_3')
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ])
                ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table_1
     * join table_2 on table_1.id = table_2.id
     * join table_3 on table_2.id = table_3.id
     * 
     */
    public function testSmartJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->join('table_3', 'inner', 'smart')
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ])
                ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table_1
     * join table_2 on table_1.id = table_2.id
     * join table_3 on table_2.id = table_3.id
     * 
     */
    public function testSimpleJoinWithMagicMethods(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->getQuery2()
                ->getQuery3()
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ])
                ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table_1
     * join table_2 on table_1.id = table_2.id
     * join table_3 on table_2.id = table_3.id
     * 
     */
    public function testSmartJoinWithMagicMethods(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->getQuery3(['algorithm' => 'smart'])
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ])
                ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table_1
     * join table_2 on table_1.id = table_2.id
     * join table_3 t3 on table_2.id = t3.id
     * 
     */
    public function testJoinByNamedRel(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q27.php'));
        $actual = Query1::all()
            ->select([
                'f1'
            ])
            ->join(NamedRel1::class)
            ->join(NamedRel2::class)
                ->select([
                    'f1' => 'af1',
                    'f2' => 'af2',
                ])
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testFailedJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q31.php'));
        $actual =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->join('table_2')
                ->join('table_4')
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ])
                ->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testJoinWithDefineOn(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q32.php'));
        $actual =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->join('table_2')
                ->join('table_4')->on("table_2.id", "table_4.id")
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ])
                ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
