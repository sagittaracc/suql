<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLGroupTest extends SuQLMock
{
    /**
     * SELECT
     *   ...
     * FROM <table>
     * ...
     * GROUP BY
     *   <table-1>.<field-1>,
     *   <table-1>.<field-2>,
     *   ...,
     *   <table-2>.<field-1>,
     *   ...
     */
    public function testSelectGroup(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                groups.name as gname,
                count(groups.name) as count
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
            where groups.name = 'admin'
            group by groups.name
SQL);

        $this->osuql->addSelect('select_group');
        $this->osuql->getQuery('select_group')->addFrom('users');
        $this->osuql->getQuery('select_group')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('select_group')->addJoin('inner', 'groups');
        $this->osuql->getQuery('select_group')->addField('groups', 'name@gname');
        $this->osuql->getQuery('select_group')->addField('groups', 'name@count');
        $this->osuql->getQuery('select_group')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('select_group')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->getQuery('select_group')->addWhere("gname = 'admin'");
        $suql = $this->osuql->getSQL(['select_group']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_group']));
    }
}