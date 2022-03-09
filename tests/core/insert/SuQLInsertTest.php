<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLInsertTest extends SuQLTest
{
    /**
     * INSERT INTO <table> (<field list>) VALUES (<actual value list>)
     */
    public function testInsert(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            insert into users (id,name) values (1,'Yuriy')
SQL);

        $this->osuql->addInsert('main');
        $this->osuql->getQuery('main')->addInto('users');
        $this->osuql->getQuery('main')->addValue('id', 1);
        $this->osuql->getQuery('main')->addValue('name', 'Yuriy');
        $suql = $this->osuql->getSQL(['main']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main']));
    }
    /**
     * INSERT INTO <table> (<field list>) VALUES (<placeholder list>)
     */
    public function testInsertWithPlaceholder(): void
    {
        $sql = StringHelper::trimSQL(<<<SQL
            insert into users (id,name) values (:id,:name)
SQL);

        $this->osuql->addInsert('main');
        $this->osuql->getQuery('main')->addInto('users');
        $this->osuql->getQuery('main')->addPlaceholder('id', ':id');
        $this->osuql->getQuery('main')->addPlaceholder('name', ':name');
        $suql = $this->osuql->getSQL(['main']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main']));
    }
}