<?php

declare(strict_types=1);

use sagittaracc\StringHelper;

final class SuQLCaseModifierTest extends SuQLMock
{
    public function testCaseModifier(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                case
                    when users.id = 1 then 'admin'
                    when users.id = 2 then 'user'
                    when users.id > 3 and groups.id < 10 then 'guest'
                    else 'nobody'
                end,
                users.name
            from users
SQL);

        $this->osuql->addSelect('select_field_list');
        $this->osuql->getQuery('select_field_list')->addFrom('users');
        $this->osuql->getQuery('select_field_list')->addField('users', 'id');
        $this->osuql->getQuery('select_field_list')->getField('users', 'id')->addModifier('test_case');
        $this->osuql->getQuery('select_field_list')->addField('users', 'name');
        $suql = $this->osuql->getSQL(['select_field_list']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_field_list']));
    }
}