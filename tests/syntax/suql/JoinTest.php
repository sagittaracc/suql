<?php

declare(strict_types=1);

use app\models\UserFullInfo;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

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
                users.id
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $query = UserFullInfo::all();
        
        $this->assertEquals($sql, $query->getRawSql());
    }
}