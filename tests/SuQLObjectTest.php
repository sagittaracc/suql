<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\SuQLObject;
use suql\core\SuQLScheme;
use suql\builder\SQLDriver;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;
use suql\core\SuQLPlaceholder;
use suql\core\SuQLSimpleParam;

final class SuQLObjectTest extends TestCase
{
    private $osuql;

    protected function setUp(): void
    {
        $scheme = new SuQLScheme();
        $scheme->rel('users', 'user_group', 'users.id = user_group.user_id');
        $scheme->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        $driver = new SQLDriver('mysql');

        $this->osuql = new SuQLObject($scheme, $driver);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }

    public function testCallbackModifier(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id
            from users
            where users.id > 3
SQL);

        $this->osuql->addSelect('callback_modifier');
        $this->osuql->getQuery('callback_modifier')->addFrom('users');
        $this->osuql->getQuery('callback_modifier')->addField('users', 'id');
        $this->osuql->getQuery('callback_modifier')->getField('users', 'id')->addCallbackModifier(function ($ofield) {
            $ofield->getOSelect()->addWhere("{$ofield->getField()} > 3");
        });
        $suql = $this->osuql->getSQL(['callback_modifier']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['callback_modifier']));
    }

    public function testFilterEmpty(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid
            from users
SQL);

        $this->osuql->addSelect('empty_filter');
        $this->osuql->getQuery('empty_filter')->addFrom('users');
        $this->osuql->getQuery('empty_filter')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('empty_filter')->addFilterWhere(':id', 'uid > :id');
        $this->osuql->setParam(':id', null);
        $suql = $this->osuql->getSQL(['empty_filter']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['empty_filter']));
    }
}
