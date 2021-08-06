<?php

declare(strict_types=1);

use test\suql\models\NoName;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\SubUnion;

final class SelectSubQueryTest extends TestCase
{
    /**
     * SELECT
     *   ...
     * FROM (
     *   SELECT
     *     ...
     *   FROM <table>
     *   ...
     * )
     */
    public function testSubQueries(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                active_groups.name,
                active_groups.count
            from (
                select
                    groups.name,
                    count(groups.name) as count
                from users
                inner join user_group on users.id = user_group.user_id
                inner join groups on user_group.group_id = groups.id
                group by groups.name
            ) active_groups
SQL);

        $query = NoName::all();

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
}