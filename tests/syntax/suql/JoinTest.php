<?php

declare(strict_types=1);

use app\models\LastRegistration;
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
                users.id,
                groups.id as gid,
                groups.name as gname
            from users
            inner join user_group on users.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $query = UserFullInfo::all();
        
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
            ) t1 on users.registration = t1.lastRegistration
SQL);
        
        $query = LastRegistration::all();
        $this->assertEquals('select max(users.registration) as lastRegistration from users', $query->getRawSql());
    }
}