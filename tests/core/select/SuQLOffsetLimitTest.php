<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLOffsetLimitTest extends SuQLTest
{
    /**
     * SELECT ... FROM <table> LIMIT <limit>
     */
    public function testSelectLimit(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.*
            from users
            limit 3
SQL);

        $this->osuql->addSelect('select_limit');
        $this->osuql->getQuery('select_limit')->addFrom('users');
        $this->osuql->getQuery('select_limit')->addField('users', '*');
        $this->osuql->getQuery('select_limit')->addOffset(0);
        $this->osuql->getQuery('select_limit')->addLimit(3);
        $suql = $this->osuql->getSQL(['select_limit']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_limit']));
    }
    /**
     * SELECT ... FROM <table> LIMIT <offset>, <limit>
     */
    public function testSelectOffsetLimit(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.*
            from users
            limit 3, 3
SQL);

        $this->osuql->addSelect('select_offset_limit');
        $this->osuql->getQuery('select_offset_limit')->addFrom('users');
        $this->osuql->getQuery('select_offset_limit')->addField('users', '*');
        $this->osuql->getQuery('select_offset_limit')->addOffset(3);
        $this->osuql->getQuery('select_offset_limit')->addLimit(3);
        $suql = $this->osuql->getSQL(['select_offset_limit']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_offset_limit']));
    }
}