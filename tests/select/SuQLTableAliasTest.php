<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLTableAliasTest extends SuQLMock
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
     * SELECT <raw sql expressions>
     */
    public function testSelectRaw(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select 2 * 2, 'Yuriy' as author
SQL);

        $this->osuql->addSelect('select_raw');
        $this->osuql->getQuery('select_raw')->addRaw("2 * 2");
        $this->osuql->getQuery('select_raw')->addRaw("'Yuriy' as author");
        $suql = $this->osuql->getSQL(['select_raw']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_raw']));
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
}