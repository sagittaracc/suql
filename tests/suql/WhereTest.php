<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\FieldName;
use suql\core\SmartDate;
use suql\core\where\Equal;
use suql\core\where\Expression;
use suql\core\where\Greater;
use suql\core\where\Less;
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
        $expected = StringHelper::trimSql(require('queries/mysql/q16.php'));
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
        $sql = require('queries/mysql/q17.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query =
            Query1::all()
                ->where([
                    'f1' => Equal::integer(1),
                    'f2' => Equal::integer(2),
                ]);

        $actualSQL = $query->getRawSql();
        $this->assertEquals($expectedSQL, $actualSQL);
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
        $sql = require('queries/mysql/q18.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);

        $query =
            Query1::all()
                ->where([
                    'f1' => Greater::integer(1),
                ])
                ->andWhere([
                    'f2' => Less::integer(2),
                ]);

        $actualSQL = $query->getRawSql();
        $this->assertEquals($expectedSQL, $actualSQL);
    }
    /**
     * 
     */
    public function testCustomExpression(): void
    {
        $sql = require('queries/mysql/q43.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);

        $query = Query1::all()->where(
            Expression::string('$1 and ($2 or $3)')
                ->addCondition(new FieldName('table_1', 'f1'), Greater::integer(1))
                ->addCondition(new FieldName('table_1', 'f2'), Greater::integer(2))
                ->addCondition(new FieldName('table_1', 'f2'), Less::integer(3))
        );

        $actualSQL = $query->getRawSql();
        $this->assertEquals($expectedSQL, $actualSQL);
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
        $sql = require('queries/mysql/q17.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query =
            Query1::find([
                'f1' => Equal::integer(1),
                'f2' => Equal::integer(2),
            ]);

        $actualSQL = $query->getRawSql();
        $this->assertEquals($expectedSQL, $actualSQL);
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
        $expected = StringHelper::trimSql(require('queries/mysql/q28.php'));

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
