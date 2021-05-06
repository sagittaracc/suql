<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;
use suql\core\SuQLInParam;
use suql\core\SuQLLikeParam;
use suql\core\SuQLSimpleParam;

final class SuQLExpressionTest extends TestCase
{
    private $fieldUserId;
    private $fieldUserName;

    private $simpleParam;
    private $betweenParam;
    private $inParam;
    private $likeParam;

    private $conditionOne;
    private $conditionTwo;
    private $conditionThree;
    private $conditionFour;

    protected function setUp(): void
    {
        $this->fieldUserId = new SuQLFieldName('users', 'id');
        $this->fieldUserName = new SuQLFieldName('users', 'name');

        $this->simpleParam = new SuQLSimpleParam($this->fieldUserId, [1]);
        $this->betweenParam = new SuQLBetweenParam($this->fieldUserId, [3, 6]);
        $this->inParam = new SuQLInParam($this->fieldUserId, [10, 20, 30]);
        $this->likeParam = new SuQLLikeParam($this->fieldUserName, ['sagittaracc']);

        $this->conditionOne = new SuQLCondition($this->simpleParam, '$ > ?');
        $this->conditionTwo = new SuQLCondition($this->betweenParam, '$ between ?');
        $this->conditionThree = new SuQLCondition($this->inParam, '$ in ?');
        $this->conditionFour = new SuQLCondition($this->likeParam, '$ like ?');
    }

    protected function tearDown(): void
    {
        $this->fieldUserId = null;
        $this->fieldUserName = null;

        $this->simpleParam = null;
        $this->betweenParam = null;
        $this->inParam = null;
        $this->likeParam = null;

        $this->conditionOne = null;
        $this->conditionTwo = null;
        $this->conditionThree = null;
        $this->conditionFour = null;
    }

    public function testSimpleExpression(): void
    {
        $expectedExpression =
            '('.
                'id > :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b '.
                    'or '.
                'id between :ph0_b90e7265948fc8b12c62f17f6f2c5363 and :ph1_da199b6888edd6f08c25ae0ea30517e8'.
            ') '.
            'and id in (:ph0_51fef196e04482fdb96e7bec99b86eda,:ph1_e0b4e60b9c0768e467e61af1ce864b27,:ph2_0bef107bc85293380a4cab23cdd72201) '.
            'and name like :ph0_c52e9ca1ce023b250556fab760727d9e';
        $expectedParams = [
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph0_b90e7265948fc8b12c62f17f6f2c5363' => 3,
            ':ph1_da199b6888edd6f08c25ae0ea30517e8' => 6,
            ':ph0_51fef196e04482fdb96e7bec99b86eda' => 10,
            ':ph1_e0b4e60b9c0768e467e61af1ce864b27' => 20,
            ':ph2_0bef107bc85293380a4cab23cdd72201' => 30,
            ':ph0_c52e9ca1ce023b250556fab760727d9e' => '%sagittaracc%',
        ];

        $actualExpression = new SuQLExpression(
            '($1 or $2) and $3 and $4',
            [$this->conditionOne, $this->conditionTwo, $this->conditionThree, $this->conditionFour]
        );
        $this->assertEquals($expectedExpression, $actualExpression->getExpression());
        $this->assertEquals($expectedParams, $actualExpression->getParams());
    }
}