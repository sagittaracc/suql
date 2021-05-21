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

    public function testStrictWhere(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid,
                users.name as uname
            from users
            where users.id % 2 = 0
SQL);

        $this->osuql->addSelect('strict_where');
        $this->osuql->getQuery('strict_where')->addFrom('users');
        $this->osuql->getQuery('strict_where')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('strict_where')->addField('users', ['name' => 'uname']);
        $this->osuql->getQuery('strict_where')->addWhere('uid % 2 = 0');
        $suql = $this->osuql->getSQL(['strict_where']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['strict_where']));
    }

    public function testExpressionWhere(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            where
                id > :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b
            and id < :ph0_b90e7265948fc8b12c62f17f6f2c5363
SQL);

        $this->osuql->addSelect('expression_where');
        $this->osuql->getQuery('expression_where')->addFrom('users');
        $this->osuql->getQuery('expression_where')->addWhere(
            new SuQLExpression('$1 and $2', [
                new SuQLCondition(new SuQLSimpleParam(new SuQLFieldName('users', 'id'), [1]), '$ > ?'),
                new SuQLCondition(new SuQLSimpleParam(new SuQLFieldName('users', 'id'), [3]), '$ < ?')
            ])
        );
        $suql = $this->osuql->getSQL(['expression_where']);

        $this->assertEquals($sql, $suql);
        $this->assertEquals([
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph0_b90e7265948fc8b12c62f17f6f2c5363' => 3,
        ], $this->osuql->getParamList());
        $this->assertNull($this->osuql->getSQL(['expression_where']));
    }

    public function testSelectWhereSubQuery(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid,
                users.name
            from users
            where users.id not in (
                select distinct
                    user_group.user_id
                from user_group
            )
SQL);

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('users');
        $this->osuql->getQuery('main_query')->addField('users', 'id@uid');
        $this->osuql->getQuery('main_query')->addField('users', 'name');
        $this->osuql->getQuery('main_query')->addWhere('uid not in @sub_query_users_belong_to_any_group');

        $this->osuql->addSelect('sub_query_users_belong_to_any_group');
        $this->osuql->getQuery('sub_query_users_belong_to_any_group')->addModifier('distinct');
        $this->osuql->getQuery('sub_query_users_belong_to_any_group')->addFrom('user_group');
        $this->osuql->getQuery('sub_query_users_belong_to_any_group')->addField('user_group', 'user_id');

        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }

    public function testSelectGroup(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                groups.name as gname,
                count(groups.name) as count
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
            where groups.name = 'admin'
            group by groups.name
SQL);

        $this->osuql->addSelect('select_group');
        $this->osuql->getQuery('select_group')->addFrom('users');
        $this->osuql->getQuery('select_group')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('select_group')->addJoin('inner', 'groups');
        $this->osuql->getQuery('select_group')->addField('groups', 'name@gname');
        $this->osuql->getQuery('select_group')->addField('groups', 'name@count');
        $this->osuql->getQuery('select_group')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('select_group')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->getQuery('select_group')->addWhere("gname = 'admin'");
        $suql = $this->osuql->getSQL(['select_group']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_group']));
    }

    public function testSubQueries(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                allGroupCount.gname,
                allGroupCount.count
            from (
                select
                    groups.name as gname,
                    count(groups.name) as count
                from users
                inner join user_group on users.id = user_group.user_id
                inner join groups on user_group.group_id = groups.id
                group by groups.name
            ) allGroupCount
            where gname = 'admin'
SQL);

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('allGroupCount');
        $this->osuql->getQuery('main_query')->addField('allGroupCount', 'gname');
        $this->osuql->getQuery('main_query')->addField('allGroupCount', 'count');
        $this->osuql->getQuery('main_query')->addWhere("gname = 'admin'");

        $this->osuql->addSelect('allGroupCount');
        $this->osuql->getQuery('allGroupCount')->addFrom('users');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'groups');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@gname');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@count');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('count');
        
        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
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
