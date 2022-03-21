<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\MySQLBuilder;
use suql\manager\EntityManager;
use test\suql\models\Query1;
use test\suql\schema\AppScheme;

final class EntityManagerTest extends TestCase
{
    private $orm;

    public function setUp(): void
    {
        $this->orm = new EntityManager();
        $this->orm->setScheme(AppScheme::class);
        $this->orm->setBuilder(MySQLBuilder::class);
    }

    public function tearDown(): void
    {
        $this->orm = null;
    }
    /**
     * Example:
     * 
     * select * from table
     * 
     */
    public function testSelect(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q4.php'));

        $query1 = $this->orm->getRepository(Query1::class);

        $actual = $query1->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
    /**
     * Example:
     * 
     * select * from table order by id
     * 
     */
    public function testOrder(): void
    {
        $query1 = $this->orm->getRepository('table_1');

        $expected = StringHelper::trimSql(require('queries/mysql/q6.php'));
        $actual = $query1->order([
            'f1' => 'desc',
            'f2' => 'asc',
        ])->getRawSql();
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
    public function testSimpleJoin(): void
    {
        $query1 = $this->orm->getRepository('table_1');

        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            $query1
                ->select(['f1'])
            ->with('table_2')
            ->with('table_3')
                ->select([
                    'f1' => 'af1',
                    'f2' => 'af2',
                ])->getRawSql();
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
        $query1 = $this->orm->getRepository('table_1');

        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            $query1
                ->select(['f1'])
            ->with('table_3', 'inner', 'smart')
                ->select([
                    'f1' => 'af1',
                    'f2' => 'af2',
                ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
