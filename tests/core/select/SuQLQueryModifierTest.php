<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLQueryModifierTest extends SuQLTest
{
    /**
     * SELECT DISTINCT ... FROM <table>
     */
    public function testSelectDistinct(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select distinct
                users.name
            from users
SQL);

        $this->osuql->addSelect('select_distinct');
        $this->osuql->getQuery('select_distinct')->addModifier('distinct');
        $this->osuql->getQuery('select_distinct')->addField('users', 'name');
        $this->osuql->getQuery('select_distinct')->addFrom('users');
        $suql = $this->osuql->getSQL(['select_distinct']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_distinct']));
    }
}