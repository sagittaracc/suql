<?php

declare(strict_types=1);

use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class QueryModifierTest extends TestCase
{
    /**
     * SELECT DISTINCT ... FROM <table>
     */
    public function testSelectDistinct(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select distinct
                users.name
            from users
SQL);

        $query = User::all()->distinct(['name']);

        $this->assertEquals($sql, $query->getRawSql());
    }
}