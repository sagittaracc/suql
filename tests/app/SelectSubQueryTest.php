<?php

declare(strict_types=1);

use app\models\NoName;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

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
}