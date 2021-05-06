<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;
use suql\core\SuQLSimpleParam;

final class SuQLExpressionTest extends TestCase
{
    private $condition_one;
    private $condition_two;

    protected function setUp(): void
    {
        $this->condition_one = new SuQLCondition(
            new SuQLSimpleParam(
                new SuQLFieldName('users', 'id'),
                [1]
            ),
            '$ > ?'
        );

        $this->condition_two = new SuQLCondition(
            new SuQLBetweenParam(
                new SuQLFieldName('groups', 'id'),
                [3, 6]
            ),
            '$ between ?',
            '%t.%n'
        );
    }

    protected function tearDown(): void
    {
        $this->condition_one = null;
        $this->condition_two = null;
    }

    public function testSimpleExpression(): void
    {
        $expectedExpression = 'id > :ph0_fc02896e3034a4ed53259916e2e2d82d and groups.id between :ph0_451045c7efc03ee0e818b41ca0601e90 and :ph1_451045c7efc03ee0e818b41ca0601e90';
        $expectedParams = [
            ':ph0_fc02896e3034a4ed53259916e2e2d82d' => 1,
            ':ph0_451045c7efc03ee0e818b41ca0601e90' => 3,
            ':ph1_451045c7efc03ee0e818b41ca0601e90' => 6,
        ];

        $actualExpression = new SuQLExpression('$1 and $2', [$this->condition_one, $this->condition_two]);
        $this->assertEquals($expectedExpression, $actualExpression->getExpression());
        $this->assertEquals($expectedParams, $actualExpression->getParams());
    }
}