<?php

declare(strict_types=1);

use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class LastRequestedModelTest extends TestCase
{
    public function testMainModel(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
SQL);

        $query = User::all();

        $this->assertEquals('test\suql\models\User', $query->getLastRequestedModel());
    }

    public function testRequestedModel(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                groups.name
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $query = User::all()
            ->select(['id'])
            ->getUserGroup()
            ->getGroup();

        $this->assertEquals('test\suql\models\Group', $query->getLastRequestedModel());
    }
}
