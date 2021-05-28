<?php

declare(strict_types=1);

use test\suql\models\User;
use test\suql\models\UserGroup;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\SimpleParam;
use suql\syntax\Expression;

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

    public function testExactExpressionWhere(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            where
                id = :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b
            and group_id = :ph0_8dcb248fff6e63eb07b5b1060a245442
SQL);

        $query =
            User::all()
                ->where([
                    'id' => 1,
                    'group_id' => 2,
                ]);
        
        $this->assertEquals($sql, $query->getRawSql());
        $this->assertEquals([
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph0_8dcb248fff6e63eb07b5b1060a245442' => 2,
        ], $query->getParamList());
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

        $query =
            User::all()
                ->where('id', '>', 1)
                ->andWhere('id', '<', 3);

        $this->assertEquals($sql, $query->getRawSql());
        $this->assertEquals([
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph0_b90e7265948fc8b12c62f17f6f2c5363' => 3,
        ], $query->getParamList());
    }

    public function testComplexExpression(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            where
                id > :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b
            and id < :ph0_b90e7265948fc8b12c62f17f6f2c5363
SQL);

        $query = User::all()->whereExpression(
            Expression::create(
                '$1 and $2', [
                    [SimpleParam::class, ['users', 'id'], '$ > ?', [1]],
                    [SimpleParam::class, ['users', 'id'], '$ < ?', [3]],
                ]
            )
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