<?php

declare(strict_types=1);

use test\suql\models\User;
use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\Condition;
use suql\core\Expression;
use suql\core\FieldName;
use suql\core\SimpleParam;
use suql\syntax\field\Field;

final class FieldModifierTest extends TestCase
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

        $query =
            User::all()
                ->select([
                    new Field('id', [
                        function($ofield) {
                            $ofield->getOSelect()->addWhere('users.id % 2 = 0');
                            
                            $ofield->getOSelect()->addWhere(
                                new Expression('$1', [
                                    new Condition(new SimpleParam(new FieldName($ofield->getTable(), $ofield->getField()), [3]), '$ > ?'),
                                ])
                            );
                        }
                    ])
                ]);
        
        $this->assertEquals($sql, $query->getRawSql());
        $this->assertEquals([
            ':ph0_e94ad661a4b7e2049ba318ed9e117616' => 3
        ], $query->getParamList());
    }
}