<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\FieldName;
use suql\core\where\Expression;
use suql\core\where\Greater;
use suql\core\where\Raw;
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
                            $ofield->getOSelect()->addWhere(null, new Raw('`table_1`.`f1` % 2 = 0'));

                            $ofield->getOSelect()->addWhere(
                                null,
                                Expression::string('$1')
                                    ->addCondition(new FieldName($ofield->getTable(), $ofield->getName()), Greater::integer(1))
                            );
                        }
                    ])
                ]);

        $actualSQL = $query->getRawSql();
        $this->assertEquals($expectedSQL, $actualSQL);
    }
}