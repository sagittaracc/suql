<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\Condition;
use suql\core\Expression;
use suql\core\FieldName;
use suql\core\param\Simple;
use suql\syntax\field\Field;
use test\suql\models\Query1;

final class FieldModifierTest extends TestCase
{
    public function testCallbackModifier(): void
    {
        $sql = require('queries/mysql/q21.php');

        $expectedSQL = StringHelper::trimSql($sql['query']);
        $expectedParams = $sql['params'];

        $query =
            Query1::all()
                ->select([
                    new Field('f1', [
                        function($ofield) {
                            $ofield->getOSelect()->addWhere('table_1.f1 % 2 = 0');
                            
                            $ofield->getOSelect()->addWhere(
                                new Expression('$1', [
                                    new Condition(new Simple(new FieldName($ofield->getTable(), $ofield->getField()), [1]), '$ > ?'),
                                ])
                            );
                        }
                    ])
                ]);

        $actualSQL = $query->getRawSql();
        $actualParams = $query->getParamList();
        
        $this->assertEquals($expectedSQL, $actualSQL);
        $this->assertEquals($expectedParams, $actualParams);
    }
}