<?php

declare(strict_types=1);

use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\FirstGroup;

final class SmartJoinTest extends TestCase
{
    /**
     * SELECT
     *   ...
     * FROM <table>
     * [INNER|LEFT|RIGHT] JOIN <join-table-1> ON <join-1>
     * [INNER|LEFT|RIGHT] JOIN <join-table-2> ON <join-2>
     * ...
     */
    public function testSmartJoinWithTable(): void
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
                ->join('groups', 'inner', 'smart')
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
     * [INNER|LEFT|RIGHT] JOIN <join-table-1> ON <join-1>
     * [INNER|LEFT|RIGHT] JOIN <join-table-2> ON <join-2>
     * [INNER|LEFT|RIGHT] JOIN (...)
     */
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
}