<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLJoinTest extends SuQLTest
{
    public function testSimpleJoin(): void
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

        $this->osuql->addSelect('simple_join');
        $this->osuql->getQuery('simple_join')->addFrom('users');
        $this->osuql->getQuery('simple_join')->addField('users', 'id');
        $this->osuql->getQuery('simple_join')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('simple_join')->addJoin('inner', 'groups');
        $this->osuql->getQuery('simple_join')->addField('groups', 'id@gid');
        $this->osuql->getQuery('simple_join')->addField('groups', 'name@gname');
        $suql = $this->osuql->getSQL(['simple_join']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['simple_join']));
    }
    /**
     * SELECT
     *   ...
     * FROM <table>
     * [INNER|LEFT|RIGHT] JOIN (
     *   SELECT ... FROM ...
     * ) <table-alias> ON <join>
     */
    public function testJoinWithSubQuery(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            inner join (
                select
                    max(users.registration) as lastRegistration
                from users
            ) t1 on users.registration = t1.lastRegistration
SQL);

        $this->osuql->getScheme()->rel('users', 't1', 'users.registration = t1.lastRegistration');

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('users');
        $this->osuql->getQuery('main_query')->addJoin('inner', 't1');

        $this->osuql->addSelect('t1');
        $this->osuql->getQuery('t1')->addFrom('users');
        $this->osuql->getQuery('t1')->addField('users', 'registration@lastRegistration');
        $this->osuql->getQuery('t1')->getField('users', 'registration@lastRegistration')->addModifier('max');

        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }
}