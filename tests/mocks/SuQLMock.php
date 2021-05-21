<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\builder\SQLDriver;
use suql\core\SuQLObject;
use suql\core\SuQLScheme;

class SuQLMock extends TestCase
{
    protected $osuql;

    protected function setUp(): void
    {
        $scheme = new SuQLScheme();
        $scheme->rel('users', 'user_group', 'users.id = user_group.user_id');
        $scheme->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        $driver = new SQLDriver('mysql');

        $this->osuql = new SuQLObject($scheme, $driver);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }
}