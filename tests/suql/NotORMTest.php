<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\NotORM;
use test\suql\schema\AppScheme;

final class NotORMTest extends TestCase
{
    public function testNotORM(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            order by users.id asc
SQL);

        $db = new NotORM();
        $query = $db->entity('users')->order(['id']);
        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testNotORMChain(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                groups.name
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $db = new NotORM(null, AppScheme::class);

        // Simple join
        $query =
            $db->entity('users')
                ->select(['id'])
            ->get('user_group')
            ->get('groups')
                ->select(['name']);

        $this->assertEquals($sql, $query->getRawSql());

        // Smart join
        $query =
            $db->entity('users')
                ->select(['id'])
            ->get('groups', 'inner', 'smart')
                ->select(['name']);

        $this->assertEquals($sql, $query->getRawSql());
    }
}
