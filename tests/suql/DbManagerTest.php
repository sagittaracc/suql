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
            from users
            order by users.id asc
SQL);

        $db = new suql\db\Manager();
        $query = $db->entity('users')->order(['id']);
        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testDbManagerTableChain(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                groups.name
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $db = new suql\db\Manager(null, AppScheme::class);

        // Simple join
        $query =
            $db->entity('users')
                ->select(['id'])
            ->with('user_group')
            ->with('groups')
                ->select(['name']);

        $this->assertEquals($sql, $query->getRawSql());

        // Smart join
        $query =
            $db->entity('users')
                ->select(['id'])
            ->with('groups', 'inner', 'smart')
                ->select(['name']);

        $this->assertEquals($sql, $query->getRawSql());
    }
}
