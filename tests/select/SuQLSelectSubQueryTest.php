<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLSelectSubQueryTest extends SuQLMock
{
    /**
     * SELECT
     *   ...
     * FROM (
     *   SELECT
     *     ...
     *   FROM <table>
     *   ...
     * )
     */
    public function testSubQueries(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                allGroupCount.gname,
                allGroupCount.count
            from (
                select
                    groups.name as gname,
                    count(groups.name) as count
                from users
                inner join user_group on users.id = user_group.user_id
                inner join groups on user_group.group_id = groups.id
                group by groups.name
            ) allGroupCount
            where gname = 'admin'
        SQL);

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('allGroupCount');
        $this->osuql->getQuery('main_query')->addField('allGroupCount', 'gname');
        $this->osuql->getQuery('main_query')->addField('allGroupCount', 'count');
        $this->osuql->getQuery('main_query')->addWhere("gname = 'admin'");

        $this->osuql->addSelect('allGroupCount');
        $this->osuql->getQuery('allGroupCount')->addFrom('users');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'groups');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@gname');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@count');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('count');
        
        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }
}