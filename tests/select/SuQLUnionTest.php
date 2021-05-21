<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLUnionTest extends SuQLMock
{
    /**
     * (SELECT ...)
     *   UNION
     * (SELECT ...)
     */
    public function testUnion(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            (select min(users.registration) as reg_interval from users)
                union
            (select max(users.registration) as reg_interval from users)
SQL);

        $this->osuql->addSelect('firstRegisration');
        $this->osuql->getQuery('firstRegisration')->addFrom('users');
        $this->osuql->getQuery('firstRegisration')->addField('users', 'registration@reg_interval');
        $this->osuql->getQuery('firstRegisration')->getField('users', 'registration@reg_interval')->addModifier('min');

        $this->osuql->addSelect('lastRegisration');
        $this->osuql->getQuery('lastRegisration')->addFrom('users');
        $this->osuql->getQuery('lastRegisration')->addField('users', 'registration@reg_interval');
        $this->osuql->getQuery('lastRegisration')->getField('users', 'registration@reg_interval')->addModifier('max');

        $this->osuql->addUnion('main_query', '@firstRegisration union @lastRegisration');
        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }
}