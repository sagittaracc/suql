<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\core\BetweenParam;
use suql\core\Condition;
use suql\core\Expression;
use suql\core\FieldName;
use suql\core\InParam;
use suql\core\LikeParam;
use suql\core\SimpleParam;

final class SuQLExpressionTest extends TestCase
{
    private $fieldF1;
    private $fieldF2;

    protected function setUp(): void
    {
        $this->fieldF1 = new FieldName('table_1', 'f1');
        $this->fieldF2 = new FieldName('table_1', 'f2');
    }

    protected function tearDown(): void
    {
        $this->fieldF1 = null;
        $this->fieldF2 = null;
    }

    public function testSimpleExpression(): void
    {
        $expectedExpression = StringHelper::trimSql(<<<SQL
            (
                f1 > :ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b
                    or
                f1 between :ph0_687821896f80eb70591ac4f812a45fe3 and :ph1_02c69713c4de4fee984a7551cf5bf2c5
            )
            and f1 in (:ph0_94db4af024845245fdf52af0ea5a922f,:ph1_9efafdf9a2b4351bc6b1bf0f4e5eade7,:ph2_05db2d0f7af12f4b3fca1f239e6f9761)
            and f2 like :ph0_2ce8d160537737958bcef91a22b01044
SQL);
        $expectedParams = [
            ':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1,
            ':ph0_687821896f80eb70591ac4f812a45fe3' => 3,
            ':ph1_02c69713c4de4fee984a7551cf5bf2c5' => 6,
            ':ph0_94db4af024845245fdf52af0ea5a922f' => 10,
            ':ph1_9efafdf9a2b4351bc6b1bf0f4e5eade7' => 20,
            ':ph2_05db2d0f7af12f4b3fca1f239e6f9761' => 30,
            ':ph0_2ce8d160537737958bcef91a22b01044' => '%sagittaracc%',
        ];

        $simpleParam = new SimpleParam($this->fieldF1, [1]);
        $betweenParam = new BetweenParam($this->fieldF1, [3, 6]);
        $inParam = new InParam($this->fieldF1, [10, 20, 30]);
        $likeParam = new LikeParam($this->fieldF2, ['sagittaracc']);

        $conditionOne = new Condition($simpleParam, '$ > ?');
        $conditionTwo = new Condition($betweenParam, '$ between ?');
        $conditionThree = new Condition($inParam, '$ in ?');
        $conditionFour = new Condition($likeParam, '$ like ?');

        $actualExpression = new Expression(
            '($1 or $2) and $3 and $4',
            [$conditionOne, $conditionTwo, $conditionThree, $conditionFour]
        );
        $this->assertEquals($expectedExpression, $actualExpression->getExpression());
        $this->assertEquals($expectedParams, $actualExpression->getParams());
    }
}