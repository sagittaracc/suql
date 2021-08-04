<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\NotORM;

final class NotORMTest extends TestCase
{
    public function testNotORM(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            order by users.id asc
SQL);

        $db = new NotORM();
        $query = $db->entity('users')->order(['id']);
        $this->assertEquals($sql, $query->getRawSql());
    }
}
