<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\schema\AppScheme;

final class DbManagerTest extends TestCase
{
    /**
     * Example:
     * 
     * select * from table order by id
     * 
     */
    public function testOrder(): void
    {
        $db = new suql\db\Manager();

        $expected = StringHelper::trimSql(require('queries/mysql/q6.php'));
        $actual = $db->entity('table_1')->order([
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
        $db = new suql\db\Manager(null, AppScheme::class);

        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            $db->entity('table_1')
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
        $db = new suql\db\Manager(null, AppScheme::class);

        $expected = StringHelper::trimSql(require('queries/mysql/q8.php'));
        $actual =
            $db->entity('table_1')
                ->select(['f1'])
            ->with('table_3', 'inner', 'smart')
                ->select([
                    'f1' => 'af1',
                    'f2' => 'af2',
                ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
