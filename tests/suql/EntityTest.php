<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\MySQLBuilder;
use test\suql\schema\AppScheme;

final class EntityTest extends TestCase
{
    /**
     * Example:
     * 
     * select * from table order by id
     * 
     */
    public function testOrder(): void
    {
        $table1 = new suql\db\Entity('table_1');

        /**
         * TODO:
         *     $table1 = new suql\db\Entity('table_1');
         * 
         *     $manager = new suql\manager\EntityManager();
         *     $manager->setBuilder({$builder});
         *     $manager->setScheme({$scheme});
         * 
         *     $repository = $manager->getRepository($table1);
         *  or
         *     $repository = $manager->getRepository(Query1::class);
         *     $repository->select([])->order()->...
         * 
         *     $repository->fetchAll();
         */

        $table1->setBuilder(MySQLBuilder::class);

        $expected = StringHelper::trimSql(require('queries/mysql/q6.php'));
        $actual = $table1->order([
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
        $table1 = new suql\db\Entity('table_1');

        $table1->setScheme(AppScheme::class);
        $table1->setBuilder(MySQLBuilder::class);

        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            $table1
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
        $table1 = new suql\db\Entity('table_1');

        $table1->setScheme(AppScheme::class);
        $table1->setBuilder(MySQLBuilder::class);

        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            $table1
                ->select(['f1'])
            ->with('table_3', 'inner', 'smart')
                ->select([
                    'f1' => 'af1',
                    'f2' => 'af2',
                ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
