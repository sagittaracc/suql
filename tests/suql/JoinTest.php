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

    public function testSmartJoin(): void
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
                ->join('table_3', 'inner', 'smart')
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ]);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSimpleJoinWithMagicMethods(): void
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
                ->getQuery2()
                ->getQuery3()
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ]);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSmartJoinWithMagicMethods(): void
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
                ->getQuery3(['algorithm' => 'smart'])
                    ->select([
                        'f1' => 'af1',
                        'f2' => 'af2',
                    ]);

        $this->assertEquals($sql, $query->getRawSql());
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
}
