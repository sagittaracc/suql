<?php

declare(strict_types=1);

use sagittaracc\StringHelper;
use suql\core\Condition;
use suql\core\FieldName;
use suql\core\SimpleParam;
use suql\core\Expression;

final class SuQLFieldModifierTest extends SuQLMock
{
    public function testCallbackModifier(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                users.id
            from users
            where users.id % 2 = 0
              and users.id > :ph0_e94ad661a4b7e2049ba318ed9e117616
SQL);

        $this->osuql->addSelect('callback_modifier');
        $this->osuql->getQuery('callback_modifier')->addFrom('users');
        $this->osuql->getQuery('callback_modifier')->addField('users', 'id');
        $this->osuql->getQuery('callback_modifier')->getField('users', 'id')->addCallbackModifier(function ($ofield) {
            $ofield->getOSelect()->addWhere('users.id % 2 = 0');

            $ofield->getOSelect()->addWhere(
                new Expression('$1', [
                    new Condition(new SimpleParam(new FieldName($ofield->getTable(), $ofield->getField()), [3]), '$ > ?'),
                ])
            );
        });
        $suql = $this->osuql->getSQL(['callback_modifier']);

        $this->assertEquals($sql, $suql);
        $this->assertEquals([
            ':ph0_e94ad661a4b7e2049ba318ed9e117616' => 3
        ], $this->osuql->getParamList());
        $this->assertNull($this->osuql->getSQL(['callback_modifier']));
    }
}