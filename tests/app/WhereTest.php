<?php

declare(strict_types=1);

use app\models\User;
use app\models\UserGroup;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\Condition;
use suql\core\Expression;
use suql\core\FieldName;
use suql\core\SimpleParam;

final class WhereTest extends TestCase
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

        $query = User::all()->select([
            'id' => 'uid',
            'name' => 'uname',
        ])->where('users.id % 2 = 0');

        $this->assertEquals($sql, $query->getRawSql());
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

        $query = User::all()->where(
            new Expression('$1 and $2', [
                new Condition(new SimpleParam(new FieldName('users', 'id'), [1]), '$ > ?'),
                new Condition(new SimpleParam(new FieldName('users', 'id'), [3]), '$ < ?'),
            ])
        );

        $this->assertEquals($sql, $query->getRawSql());
        $this->assertEquals([
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph0_b90e7265948fc8b12c62f17f6f2c5363' => 3,
        ], $query->getParamList());
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

        $query = User::all()->select([
            'id' => 'uid',
            'name',
        ])->where('users.id not in ?', [UserGroup::all()->distinct()->select(['user_id'])]);

        $this->assertEquals($sql, $query->getRawSql());
    }
}