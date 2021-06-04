<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class SuQLSmartJoinTest extends SuQLMock
{
    public function testSmartJoinWithTable(): void
    {
        /**
         * SELECT
         *   ...
         * FROM <table>
         * [INNER|LEFT|RIGHT] JOIN <join-table-1> ON <join-1>
         * [INNER|LEFT|RIGHT] JOIN <join-table-2> ON <join-2>
         * ...
         */
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                groups.id as gid,
                groups.name as gname
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $this->osuql->addSelect('smart_join');
        $this->osuql->getQuery('smart_join')->addFrom('users');
        $this->osuql->getQuery('smart_join')->addField('users', 'id');
        $this->osuql->getQuery('smart_join')->addSmartJoin('users', 'groups');
        $this->osuql->getQuery('smart_join')->addField('groups', 'id@gid');
        $this->osuql->getQuery('smart_join')->addField('groups', 'name@gname');
        $suql = $this->osuql->getSQL(['smart_join']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['smart_join']));
    }

    public function testSmartJoinWithView(): void
    {
        /**
         * SELECT
         *   ...
         * FROM <table>
         * [INNER|LEFT|RIGHT] JOIN <join-table-1> ON <join-1>
         * [INNER|LEFT|RIGHT] JOIN <join-table-2> ON <join-2>
         * [INNER|LEFT|RIGHT] JOIN (...)
         */
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id
            from users
            inner join user_group on users.id = user_group.user_id
            inner join (
                select
                    *
                from groups
                limit 1
            ) first_group on user_group.group_id = first_group.id
SQL);

        $this->osuql->getScheme()->rel('user_group', 'first_group', 'user_group.group_id = first_group.id');

        $this->osuql->addSelect('smart_join');
        $this->osuql->getQuery('smart_join')->addFrom('users');
        $this->osuql->getQuery('smart_join')->addField('users', 'id');
        $this->osuql->getQuery('smart_join')->addSmartJoin('users', 'first_group');

        $this->osuql->addSelect('first_group');
        $this->osuql->getQuery('first_group')->addFrom('groups');
        $this->osuql->getQuery('first_group')->addLimit(1);

        $suql = $this->osuql->getSQL(['smart_join']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['smart_join']));
    }
}