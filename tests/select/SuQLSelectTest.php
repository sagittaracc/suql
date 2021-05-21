<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLSelectTest extends SuQLMock
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
        $this->osuql->getQuery('select_all')->addFrom('users');
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
        $this->osuql->getQuery('select_all_with_table_name')->addFrom('users');
        $this->osuql->getQuery('select_all_with_table_name')->addField('users', '*');
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
        $this->osuql->getQuery('select_field_list')->addFrom('users');
        $this->osuql->getQuery('select_field_list')->addField('users', 'id');
        $this->osuql->getQuery('select_field_list')->addField('users', 'name');
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
        $this->osuql->getQuery('select_using_aliases')->addFrom('users');
        $this->osuql->getQuery('select_using_aliases')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('select_using_aliases')->addField('users', 'name@uname'); // just another way to set an alias
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
        $this->osuql->getQuery('select_raw')->addField(null, "2 * 2");
        $this->osuql->getQuery('select_raw')->addField(null, "'Yuriy' as author");
        $suql = $this->osuql->getSQL(['select_raw']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_raw']));
    }
}