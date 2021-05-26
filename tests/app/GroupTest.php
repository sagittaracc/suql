<?php

declare(strict_types=1);

use app\models\ActiveGroups;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class GroupTest extends TestCase
{
    /**
     * SELECT
     *   ...
     * FROM <table>
     * ...
     * GROUP BY
     *   <table-1>.<field-1>,
     *   <table-1>.<field-2>,
     *   ...,
     *   <table-2>.<field-1>,
     *   ...
     */
    public function testSelectGroup(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                groups.name,
                count(groups.name) as count
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
            group by groups.name
SQL);

        $query = ActiveGroups::all();
        
        $this->assertEquals($sql, $query->getRawSql());
    }
}