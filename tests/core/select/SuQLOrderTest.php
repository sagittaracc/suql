<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLOrderTest extends SuQLTest
{
    /**
     * SELECT
     *   ...
     * FROM <table>
     * ORDER BY
     *   <table>.<field-1> [DESC|ASC],
     *   <table>.<field-2> [DESC|ASC],
     *   ...
     */
    public function testSelectOrder(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            order by users.name desc, users.id asc
SQL);

        $this->osuql->addSelect('select_order');
        $this->osuql->getQuery('select_order')->addFrom('users');
        $this->osuql->getQuery('select_order')->addField('users', 'name', false);
        $this->osuql->getQuery('select_order')->addField('users', 'id', false);
        $this->osuql->getQuery('select_order')->getField('users', 'name')->addModifier('desc');
        $this->osuql->getQuery('select_order')->getField('users', 'id')->addModifier('asc');
        $suql = $this->osuql->getSQL(['select_order']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_order']));
    }
}