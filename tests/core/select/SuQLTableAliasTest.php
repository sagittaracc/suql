<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLTableAliasTest extends SuQLTest
{
    /**
     * SELECT * FROM <table>
     */
    public function testSelectAll(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
SQL);

        $this->osuql->addSelect('select_all');
        $this->osuql->getQuery('select_all')->addFrom('{{u}}');
        $suql = $this->osuql->getSQL(['select_all']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_all']));
    }
    /**
     * SELECT <table>.* FROM <table>
     */
    public function testSelectAllWithTableName(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.*
            from users
SQL);

        $this->osuql->addSelect('select_all_with_table_name');
        $this->osuql->getQuery('select_all_with_table_name')->addFrom('{{u}}');
        $this->osuql->getQuery('select_all_with_table_name')->addField('{{u}}', '*');
        $suql = $this->osuql->getSQL(['select_all_with_table_name']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_all_with_table_name']));
    }
    /**
     * SELECT
     *   <table>.<field-1>,
     *   <table>.<field-2>
     * FROM <table>
     */
    public function testSelectFieldList(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                users.name
            from users
SQL);

        $this->osuql->addSelect('select_field_list');
        $this->osuql->getQuery('select_field_list')->addFrom('{{u}}');
        $this->osuql->getQuery('select_field_list')->addField('{{u}}', 'id');
        $this->osuql->getQuery('select_field_list')->addField('{{u}}', 'name');
        $suql = $this->osuql->getSQL(['select_field_list']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_field_list']));
    }
    /**
     * SELECT
     *   <table>.<field-1> AS <alias-1>,
     *   <table>.<field-2> AS <alias-2>
     * FROM <table>
     */
    public function testSelectUsingAliases(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid,
                users.name as uname
            from users
SQL);

        $this->osuql->addSelect('select_using_aliases');
        $this->osuql->getQuery('select_using_aliases')->addFrom('{{u}}');
        $this->osuql->getQuery('select_using_aliases')->addField('{{u}}', ['id' => 'uid']);
        $this->osuql->getQuery('select_using_aliases')->addField('{{u}}', 'name@uname'); // just another way to set an alias
        $suql = $this->osuql->getSQL(['select_using_aliases']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_using_aliases']));
    }
    /**
     * SELECT
     *   <table>.<field>,
     *   ...,
     *   <raw sql expression>
     * FROM <table>
     */
    public function testSelectWithRaw(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.*,
                'Yuriy' as author
            from users
SQL);

        $this->osuql->addSelect('select_with_raw');
        $this->osuql->getQuery('select_with_raw')->addFrom('{{u}}');
        $this->osuql->getQuery('select_with_raw')->addField('{{u}}', '*');
        $this->osuql->getQuery('select_with_raw')->addRaw("'Yuriy' as author");
        $suql = $this->osuql->getSQL(['select_with_raw']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_with_raw']));
    }

    public function testRenameQuery(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
SQL);

        $this->osuql->addSelect('select_all');
        $this->osuql->getQuery('select_all')->addFrom('{{u}}');
        $this->osuql->renameQuery('select_all', 'new_select_all');
        $suqlNew = $this->osuql->getSQL(['new_select_all']);

        $this->assertEquals($sql, $suqlNew);
        $this->assertNull($this->osuql->getSQL(['new_select_all']));
    }
    public function testSimpleJoin(): void
    {
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
        $this->osuql->getQuery('simple_join')->addFrom('{{u}}');
        $this->osuql->getQuery('simple_join')->addField('{{u}}', 'id');
        $this->osuql->getQuery('simple_join')->addJoin('inner', '{{ug}}');
        $this->osuql->getQuery('simple_join')->addJoin('inner', '{{g}}');
        $this->osuql->getQuery('simple_join')->addField('{{g}}', 'id@gid');
        $this->osuql->getQuery('simple_join')->addField('{{g}}', 'name@gname');
        $suql = $this->osuql->getSQL(['simple_join']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['simple_join']));
    }
    
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

        $this->osuql->getScheme()->rel('{{u}}', 't1', '{{u}}.registration = t1.lastRegistration');

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('{{u}}');
        $this->osuql->getQuery('main_query')->addJoin('inner', 't1');

        $this->osuql->addSelect('t1');
        $this->osuql->getQuery('t1')->addFrom('{{u}}');
        $this->osuql->getQuery('t1')->addField('{{u}}', 'registration@lastRegistration');
        $this->osuql->getQuery('t1')->getField('{{u}}', 'registration@lastRegistration')->addModifier('max');

        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }

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
        $this->osuql->getQuery('select_group')->addFrom('{{u}}');
        $this->osuql->getQuery('select_group')->addJoin('inner', '{{ug}}');
        $this->osuql->getQuery('select_group')->addJoin('inner', '{{g}}');
        $this->osuql->getQuery('select_group')->addField('{{g}}', 'name@gname');
        $this->osuql->getQuery('select_group')->addField('{{g}}', 'name@count');
        $this->osuql->getQuery('select_group')->getField('{{g}}', 'name@count')->addModifier('group');
        $this->osuql->getQuery('select_group')->getField('{{g}}', 'name@count')->addModifier('count');
        $this->osuql->getQuery('select_group')->addWhere("{{g}}.name = 'admin'");
        $suql = $this->osuql->getSQL(['select_group']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_group']));
    }
}