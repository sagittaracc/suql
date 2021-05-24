<?php

declare(strict_types=1);

use app\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;

final class SelectTest extends TestCase
{
    public function testSimpleModel(): void
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