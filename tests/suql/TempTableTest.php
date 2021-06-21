<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\TempTable;

final class TempTableTest extends TestCase
{
    public function testTempTable(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                groups.name
            from temp_table
            inner join user_group on temp_table.id = user_group.user_id
            inner join groups on user_group.group_id = groups.id
SQL);

        $tableData = [
            ['id' => 1, 'name' => 'mario'],
            ['id' => 2, 'name' => 'fayword'],
            ['id' => 3, 'name' => '1nterFucker'],
        ];

        // TODO: Доработать обработку метода load при fetching данных
        $query = TempTable::load($tableData)->getGroup(['algorithm' => 'smart']);

        $this->assertEquals($sql, $query->getRawSql());
    }
}