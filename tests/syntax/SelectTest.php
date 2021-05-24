<?php

declare(strict_types=1);

use app\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class SelectTest extends TestCase
{
    /**
     * SELECT
     *   <table>.<field-1>,
     *   <table>.<field-2>
     * FROM <table>
     */
    public function testSelectFieldList(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id,
                users.name
            from users
SQL);

        $query = User::all()->select(['id', 'name']);

        $this->assertEquals($sql, $query->getRawSql());
    }
    /**
     * SELECT
     *   <table>.<field-1> AS <alias-1>,
     *   <table>.<field-2> AS <alias-2>
     * FROM <table>
     */
    public function testSelectUsingAliases(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id as uid,
                users.name as uname
            from users
SQL);

        $query = User::all()->select([
            'id' => 'uid',
            'name' => 'uname',
        ]);

        $this->assertEquals($sql, $query->getRawSql());
    }
}