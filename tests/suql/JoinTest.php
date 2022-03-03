<?php

declare(strict_types=1);

use test\suql\models\LastRegistration;
use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\schema\NamedRel2;
use test\suql\schema\NamedRel3;

final class JoinTest extends TestCase
{
    /**
     * SELECT
     *   ...
     * FROM <table>
     * [INNER|LEFT|RIGHT] JOIN <join-table-1> ON <join-1>
     * [INNER|LEFT|RIGHT] JOIN <join-table-2> ON <join-2>
     * ...
     */
    public function testSimpleJoin(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                groups.id as gid,
                groups.name as gname
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $query =
            User::all()
                ->select([
                    'id',
                ])
                ->join('user_group')
                ->join('groups')
                    ->select([
                        'id' => 'gid',
                        'name' => 'gname',
                    ]);

        $this->assertEquals($sql, $query->getRawSql());
    }
    /**
     * SELECT
     *   ...
     * FROM <table>
     * [INNER|LEFT|RIGHT] JOIN (
     *   SELECT ... FROM ...
     * ) <table-alias> ON <join>
     */
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

    public function testOrmChain(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                groups.name
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        // Simple join
        $this->assertEquals(
            $sql,
            User::all()
                ->select(['id'])
                ->getUserGroup()
                ->getGroup()
                ->getRawSql()
        );
        // Smart join
        $this->assertEquals(
            $sql,
            User::all()
                ->select(['id'])
                ->getGroup([
                    'algorithm' => 'smart'
                ])->getRawSql()
        );
    }

    public function testJoinByNamedRel(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from table_1
            inner join table_2 on table_1.id = table_2.id
            left join table_3 on table_2.id = table_3.id
SQL);

        $query = Query1::all()->join(NamedRel2::class)->join(NamedRel3::class, 'left');

        $this->assertEquals($sql, $query->getRawSql());
    }
}
