<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\Field;
use test\suql\models\UnionQuery;
use test\suql\models\User;

final class UnionTest extends TestCase
{
    /**
     * (SELECT ...)
     *   UNION
     * (SELECT ...)
     */
    public function testUnion(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            (select min(users.registration) as reg_interval from users)
                union
            (select max(users.registration) as reg_interval from users)
SQL);

        $query = UnionQuery::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
}