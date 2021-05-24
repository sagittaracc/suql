<?php

declare(strict_types=1);

use app\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class OrderTest extends TestCase
{
    /**
     * SELECT
     *   ...
     * FROM <table>
     * ORDER BY
     *   <table>.<field-1> [DESC|ASC],
     *   <table>.<field-2> [DESC|ASC],
     *   ...
     */
    public function testSelectOrder(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            order by users.name desc, users.id asc
SQL);

        $query = User::all()->order([
            'name' => 'desc',
            'id' => 'asc',
        ]);

        $this->assertEquals($sql, $query->getRawSql());
    }
}