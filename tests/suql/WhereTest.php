<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\SimpleParam;
use suql\core\SmartDate;
use suql\syntax\Expression;
use test\suql\models\Query1;

final class WhereTest extends TestCase
{
    /**
     * Example:
     * 
     * select
     *     table.f1 as af1,
     *     table.f2 as af2
     * from table
     * where table.f1 % 2 = 0
     * 
     */
    public function testStringWhere(): void
    {
        $expected = StringHelper::trimSql(require('queries/q16.php'));
        $actual = Query1::all()->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ])->where('table_1.f1 % 2 = 0')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table
     * where f1 = 1 and f2 = 2
     * 
     */
    public function testEqualWhere(): void
    {
        $sql = require('queries/q17.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query =
            Query1::all()
                ->where([
                    'f1' => 1,
                    'f2' => 2,
                ]);

        $actualSQL = $query->getRawSql();
        $actualParams = $query->getParamList();

        $this->assertEquals($expectedSQL, $actualSQL);
        $this->assertEquals($expectedParams, $actualParams);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table
     * where f1 > 1 and f2 < 2
     * 
     */
    public function testExpressionWhere(): void
    {
        $sql = require('queries/q18.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query =
            Query1::all()
                ->where('f1', '>', 1)
                ->andWhere('f2', '<', 2);

        $actualSQL = $query->getRawSql();
        $actualParams = $query->getParamList();

        $this->assertEquals($expectedSQL, $actualSQL);
        $this->assertEquals($expectedParams, $actualParams);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table
     * where
     *     f1 > :ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b and
     *     (
     *         f2 > :ph0_52b577f9a00337a21f2f63d83847c058 or
     *         f2 < :ph0_df41dd88e94a1d6f56fb80165480688b
     *     )
     * 
     */
    public function testCustomExpression(): void
    {
        $sql = require('queries/q19.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query = Query1::all()->whereExpression(
            Expression::create(
                '$1 and ($2 or $3)',
                [
                    [SimpleParam::class, ['table_1', 'f1'], '$ > ?', [1]],
                    [SimpleParam::class, ['table_1', 'f2'], '$ > ?', [2]],
                    [SimpleParam::class, ['table_1', 'f2'], '$ < ?', [3]],
                ]
            )
        );

        $actualSQL = $query->getRawSql();
        $actualParams = $query->getParamList();

        $this->assertEquals($expectedSQL, $actualSQL);
        $this->assertEquals($expectedParams, $actualParams);
    }
    /**
     * Example:
     * 
     * select
     *     *
     * from table
     * where f1 = 1 and f2 = 2
     * 
     */
    public function testFindMethod(): void
    {
        $sql = require('queries/q17.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query =
            Query1::find([
                'f1' => 1,
                'f2' => 2,
            ]);

        $actualSQL = $query->getRawSql();
        $actualParams = $query->getParamList();

        $this->assertEquals($expectedSQL, $actualSQL);
        $this->assertEquals($expectedParams, $actualParams);
    }
    /**
     * Example:
     * 
     * select
     *     table.f1,
     *     table.f2
     * from table
     * where `table`.`f1` >= DATE_ADD(CURDATE(), INTERVAL -3 day)
     * 
     */
    public function testWhere20(): void
    {
        $expected = StringHelper::trimSql(require('queries/q28.php'));

        $query =
            Query1::find()
                ->select(['f1', 'f2'])
                ->where([
                    'f1' => SmartDate::create('last 3 days')
                ]);

        $actual = $query->getRawSql();

        $this->assertEquals($expected, $actual);
    }
}
