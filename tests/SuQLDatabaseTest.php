<?php

declare(strict_types=1);

use app\model\Group;
use app\model\User;
use app\model\UserGroup;
use PHPUnit\Framework\TestCase;
use suql\core\SuQLDatabase;
use suql\core\SuQLScheme;

final class SuQLDatabaseTest extends TestCase
{
    public function testDatabase(): void
    {
        $scheme = new SuQLScheme();
        $scheme->rel('users', 'user_group', 'users.id = user_group.user_id');
        $scheme->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        $database = new SuQLDatabase();
        $database->setScheme($scheme);
        $database->addModel(User::class);
        $database->addModel(UserGroup::class);
        $database->addModel(Group::class);

        $this->assertTrue(true);
    }
}
