<?php

declare(strict_types=1);

use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class SelectTest extends TestCase
{
    /**
     * SELECT * FROM <table>
     */
    public function testSelectAll(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from users
SQL);

        $query = User::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
    /**
     * SELECT <table>.* FROM <table>
     */
    public function testSelectAllWithTableName(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.*
            from users
SQL);

        $query = User::all()->select(['*']);

        $this->assertEquals($sql, $query->getRawSql());
    }
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