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
    private $fieldUserId;
    private $fieldUserName;

    protected function setUp(): void
    {
        $this->fieldUserId = new FieldName('users', 'id');
        $this->fieldUserName = new FieldName('users', 'name');
    }

    protected function tearDown(): void
    {
        $this->fieldUserId = null;
        $this->fieldUserName = null;
    }

    public function testSimpleExpression(): void
    {
        $expectedExpression = StringHelper::trimSql(<<<SQL
            (
                id > :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b
                    or
                id between :ph0_b90e7265948fc8b12c62f17f6f2c5363 and :ph1_da199b6888edd6f08c25ae0ea30517e8
            )
            and id in (:ph0_51fef196e04482fdb96e7bec99b86eda,:ph1_e0b4e60b9c0768e467e61af1ce864b27,:ph2_0bef107bc85293380a4cab23cdd72201)
            and name like :ph0_c52e9ca1ce023b250556fab760727d9e
SQL);
        $expectedParams = [
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph0_b90e7265948fc8b12c62f17f6f2c5363' => 3,
            ':ph1_da199b6888edd6f08c25ae0ea30517e8' => 6,
            ':ph0_51fef196e04482fdb96e7bec99b86eda' => 10,
            ':ph1_e0b4e60b9c0768e467e61af1ce864b27' => 20,
            ':ph2_0bef107bc85293380a4cab23cdd72201' => 30,
            ':ph0_c52e9ca1ce023b250556fab760727d9e' => '%sagittaracc%',
        ];

        $simpleParam = new SimpleParam($this->fieldUserId, [1]);
        $betweenParam = new BetweenParam($this->fieldUserId, [3, 6]);
        $inParam = new InParam($this->fieldUserId, [10, 20, 30]);
        $likeParam = new LikeParam($this->fieldUserName, ['sagittaracc']);

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