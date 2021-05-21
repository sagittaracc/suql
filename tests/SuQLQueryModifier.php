<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\SQLDriver;
use suql\core\SuQLObject;
use suql\core\SuQLScheme;

final class SuQLQueryModifier extends TestCase
{
    private $osuql;

    protected function setUp(): void
    {
        $scheme = new SuQLScheme();
        $driver = new SQLDriver('mysql');

        $this->osuql = new SuQLObject($scheme, $driver);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }
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