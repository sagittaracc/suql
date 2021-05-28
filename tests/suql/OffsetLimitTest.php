<?php

declare(strict_types=1);

use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class OffsetLimitTest extends TestCase
{
    /**
     * SELECT ... FROM <table> LIMIT <limit>
     */
    public function testSelectLimit(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            limit 3
SQL);

        $query = User::all()->limit(3);

        $this->assertEquals($sql, $query->getRawSql());
    }
    /**
     * SELECT ... FROM <table> LIMIT <offset>, <limit>
     */
    public function testSelectOffsetLimit(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
            limit 3, 3
SQL);
        $query =
            User::all()
                ->offset(3)
                ->limit(3);

        $this->assertEquals($sql, $query->getRawSql());
    }
}