<?php

declare(strict_types=1);

use sagittaracc\StringHelper;
use suql\core\FieldName;
use suql\core\SmartDate;

final class SuQLWhere20Test extends SuQLTest
{
    public function testWhere20(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select * from table_1
            where `table_1`.`f1` >= DATE_ADD(CURDATE(), INTERVAL -3 day)
SQL);

        $this->osuql->addSelect('query1');
        $this->osuql->getQuery('query1')->addFrom('table_1');
        $this->osuql->getQuery('query1')->addWhere20(
            new FieldName('table_1', 'f1'),
            SmartDate::create('last 3 days')
        );
        $suql = $this->osuql->getSQL(['query1']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['query1']));
    }
}