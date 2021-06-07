<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\builder\MySQLBuilder;
use suql\core\Obj;
use suql\core\Scheme;

class SuQLMock extends TestCase
{
    protected $osuql;

    protected function setUp(): void
    {
        $scheme = new Scheme();

        $scheme->addTableList([
            'users' => 'u',
            'user_group' => 'ug',
            'groups' => 'g',
        ]);

        $scheme->rel('users', 'user_group', 'users.id = user_group.user_id');
        $scheme->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        $builder = new MySQLBuilder();

        $this->osuql = new Obj($scheme, $builder);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }
}