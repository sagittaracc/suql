<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLInsertTest extends SuQLTest
{
    public function testInsert(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            insert into table_1 (f1,f2) values (1,'Yuriy Arutyunyan')
SQL);

        $this->osuql->addInsert('query_1');
        $this->osuql->getQuery('query_1')->addInto('table_1');
        $this->osuql->getQuery('query_1')->addValue('f1', 1);
        $this->osuql->getQuery('query_1')->addValue('f2', 'Yuriy Arutyunyan');
        $suql = $this->osuql->getSQL(['query_1']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['query_1']));
    }

    public function testInsertWithPlaceholder(): void
    {
        $sql = StringHelper::trimSQL(<<<SQL
            insert into table_1 (f1,f2) values (:f1,:f2)
SQL);

        $this->osuql->addInsert('query_1');
        $this->osuql->getQuery('query_1')->addInto('table_1');
        $this->osuql->getQuery('query_1')->addPlaceholder('f1', ':f1');
        $this->osuql->getQuery('query_1')->addPlaceholder('f2', ':f2');
        $suql = $this->osuql->getSQL(['query_1']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['query_1']));
    }
}