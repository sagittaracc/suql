<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;

final class SuQLExpressionTest extends TestCase
{
    private $field_userId;
    private $field_groupId;

    protected function setUp(): void
    {
        $this->field_userId = new SuQLFieldName('users', 'id');
        $this->field_groupId = new SuQLFieldName('groups', 'id');
    }

    protected function tearDown(): void
    {
        $this->field_userId = null;
        $this->field_groupId = null;
    }

    public function testSimpleExpression(): void
    {
        $expectedExpression = 'id > 1 and id < 3';

        $condition_one = new SuQLCondition($this->field_userId, '$ > 1');
        $condition_two = new SuQLCondition($this->field_userId, '$ < 3');

        $actualExpression = new SuQLExpression('$1 and $2', [$condition_one, $condition_two]);
        $this->assertEquals($expectedExpression, $actualExpression);
    }

    public function testExpressionUsingTwoDifferentTables(): void
    {
        $expectedExpression = 'users.id > 0 or groups.id > 0';

        $condition_one = new SuQLCondition($this->field_userId, '$ > 0', '%t.%n');
        $condition_two = new SuQLCondition($this->field_groupId, '$ > 0', '%t.%n');

        $actualExpression = new SuQLExpression('$1 or $2', [$condition_one, $condition_two]);
        $this->assertEquals($expectedExpression, $actualExpression);
    }
}