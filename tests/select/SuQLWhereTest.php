<?php

declare(strict_types=1);

use sagittaracc\StringHelper;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;
use suql\core\SuQLSimpleParam;

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
}