<?php

declare(strict_types=1);

use sagittaracc\StringHelper;
use suql\core\Condition;
use suql\core\Expression;
use suql\core\FieldName;
use suql\core\Placeholder;
use suql\core\SimpleParam;

final class SuQLWhereTest extends SuQLMock
{
    public function testStringWhere(): void
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
            new Expression('$1 and $2', [
                new Condition(new SimpleParam(new FieldName('users', 'id'), [1]), '$ > ?'),
                new Condition(new SimpleParam(new FieldName('users', 'id'), [3]), '$ < ?')
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

    public function testPlaceholderExpressionWhere(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            where
                id > :id1
            and id < :id2
SQL);

        $this->osuql->addSelect('placeholder_expression_where');
        $this->osuql->getQuery('placeholder_expression_where')->addFrom('users');
        $this->osuql->getQuery('placeholder_expression_where')->addWhere(
            new Expression('$1 and $2', [
                new Condition(new SimpleParam(new FieldName('users', 'id'), [new Placeholder('id1')]), '$ > ?'),
                new Condition(new SimpleParam(new FieldName('users', 'id'), [new Placeholder('id2')]), '$ < ?'),
            ])
        );
        $suql = $this->osuql->getSQL(['placeholder_expression_where']);

        $this->assertEquals($sql, $suql);
        $this->assertEquals([], $this->osuql->getParamList());
        $this->assertNull($this->osuql->getSQL(['placeholder_expression_where']));
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

    public function testFilterEmpty(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid
            from users
SQL);

        $filter = new SimpleParam(new FieldName('users', 'id'), [null]);

        $this->osuql->addSelect('empty_filter');
        $this->osuql->getQuery('empty_filter')->addFrom('users');
        $this->osuql->getQuery('empty_filter')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('empty_filter')->addFilterWhere(':id', 'uid > :id');
        $this->osuql->setParam(':id', $filter);
        $suql = $this->osuql->getSQL(['empty_filter']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['empty_filter']));
    }

    public function testFilterNotEmpty(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid
            from users
            where users.id > :id
SQL);

        $filter = new SimpleParam(new FieldName('users', 'id'), [3]);

        $this->osuql->addSelect('not_empty_filter');
        $this->osuql->getQuery('not_empty_filter')->addFrom('users');
        $this->osuql->getQuery('not_empty_filter')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('not_empty_filter')->addFilterWhere(':id', 'uid > :id');
        $this->osuql->setParam(':id', $filter);
        $suql = $this->osuql->getSQL(['not_empty_filter']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['not_empty_filter']));
    }
}