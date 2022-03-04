<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\schema\AppScheme;

final class DbManagerTest extends TestCase
{
    public function testDbManager(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from table_1
            order by table_1.f1 asc
SQL);

        $db = new suql\db\Manager();
        $query = $db->entity('table_1')->order(['f1']);
        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testDbManagerSimpleJoin(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_1.f1,
                table_3.f1
            from table_1
            inner join table_2 on table_1.id = table_2.id
            inner join table_3 on table_2.id = table_3.id
SQL);

        $db = new suql\db\Manager(null, AppScheme::class);

        $query =
            $db->entity('table_1')
                ->select(['f1'])
            ->with('table_2')
            ->with('table_3')
                ->select(['f1']);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testDbManagerSmartJoin(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_1.f1,
                table_3.f1
            from table_1
            inner join table_2 on table_1.id = table_2.id
            inner join table_3 on table_2.id = table_3.id
SQL);

        $db = new suql\db\Manager(null, AppScheme::class);

        $query =
            $db->entity('table_1')
                ->select(['f1'])
            ->with('table_3', 'inner', 'smart')
                ->select(['f1']);

        $this->assertEquals($sql, $query->getRawSql());
    }
}
