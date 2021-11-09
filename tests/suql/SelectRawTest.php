<?php

declare(strict_types=1);

use test\suql\models\RawQuery;
use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\field\Raw;

final class SelectRawTest extends TestCase
{
    /**
     * SELECT <raw sql expressions>
     */
    public function testSelectRaw(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select 2 * 2, 'Yuriy' as author
SQL);

        $query = RawQuery::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
    /**
     * SELECT
     *   <table>.<field>,
     *   ...,
     *   <raw sql expression>
     * FROM <table>
     */
    public function testSelectWithRaw(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.*,
                'Yuriy' as author
            from users
SQL);

        $query = User::all()->select([
            '*',
            Raw::expression("'Yuriy' as author"),
        ]);

        $this->assertEquals($sql, $query->getRawSql());
    }
}