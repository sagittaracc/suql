<?php

declare(strict_types=1);

use test\suql\models\LastRegistration;
use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\schema\NamedRel1;
use test\suql\schema\NamedRel2;

final class JoinTest extends TestCase
{
    public function testSimpleJoin(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_1.f1,
                table_3.f1 as af1,
                table_3.f2 as af2
            from table_1
            inner join table_2 on table_1.id = table_2.id
            inner join table_3 on table_2.id = table_3.id
SQL);

        $query =
            Query1::all()
                ->select([
                    'f1',
                ])
                ->join('table_2')
                ->join('table_3')
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
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

        $query = Query1::all()->join(NamedRel1::class)->join(NamedRel2::class, 'left');

        $this->assertEquals($sql, $query->getRawSql());
    }
}
