<?php

declare(strict_types=1);

use app\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\Modifier;

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

        $query =
            User::all()
                ->join('user_group')
                ->join('groups')
                    ->select([
                        'name',
                        (new Modifier('count'))->applyTo(['name' => 'count']),
                    ])
                ->group('name');
        
        $this->assertEquals($sql, $query->getRawSql());
    }
}