<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\FirstGroup;
use test\suql\models\LastRegistration;
use test\suql\models\Query1;
use test\suql\models\SubUnion;
use test\suql\models\User;
use test\suql\models\UserGroup;

final class ComplexQueryTest extends TestCase
{
    public function testSelectGroupWithJoin(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_3.f1,
                count(table_3.f1) as count
            from table_1
            inner join table_2 on table_1.id = table_2.id
            inner join table_3 on table_2.id = table_3.id
            group by table_3.f1
SQL);

        $query = Query1::all()
            ->getQuery2()
            ->getQuery3()
                ->select(['f1'])
            ->group('f1')
            ->count(['f1' => 'count']);
        
        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testJoinWithSubQuery(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            inner join (
                select
                    max(users.registration) as lastRegistration
                from users
            ) last_registration on users.registration = last_registration.lastRegistration
SQL);

        $this->assertEquals(
            $sql,
            User::all()
                ->join(LastRegistration::all())
                ->getRawSql()
        );

        $this->assertEquals(
            $sql,
            User::all()
                ->getLastRegistration()
                ->getRawSql()
        );
    }
    public function testSmartJoinWithView(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id
            from users
            inner join user_group on users.id = user_group.user_id
            inner join (
                select
                    groups.*
                from groups
                limit 1
            ) first_group on user_group.group_id = first_group.id
SQL);

        $query =
            User::all()
                ->select([
                    'id',
                ])
                ->join(FirstGroup::all(), 'inner', 'smart');
        
        $this->assertEquals($sql, $query->getRawSql());

        $query =
            User::all()
                ->select([
                    'id',
                ])
                ->getFirstGroup([
                    'algorithm' => 'smart',
                ]);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSubUnion(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select * from (
                (select min(users.registration) as reg_interval from users)
                union
                (select max(users.registration) as reg_interval from users)
            ) last_registration
SQL);
        $query = SubUnion::all();

        $this->assertEquals($sql, $query->getRawSql());
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
        ])->where('users.id not in ?', [UserGroup::all()->distinct(['user_id'])]);

        $this->assertEquals($sql, $query->getRawSql());
    }
}