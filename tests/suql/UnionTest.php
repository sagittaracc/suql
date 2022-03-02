<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\field\Field;
use test\suql\models\SimpleQuery;
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

    public function testSomeUnion(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            (select table_1.field_1, table_1.field_2, table_1.field_3 from table_1)
                union
            (select table_1.field_4, table_1.field_5, table_1.field_6 from table_1)
SQL);
        $query1 = SimpleQuery::all()->select(['field_1', 'field_2', 'field_3'])->as('query1');
        $query2 = SimpleQuery::all()->select(['field_4', 'field_5', 'field_6'])->as('query2');

        $query = $query1->and([$query2]);

        $this->assertEquals($sql, $query->getRawSql());
    }
}