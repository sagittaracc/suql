<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLCondition;
use suql\core\SuQLExpression;
use suql\core\SuQLFieldName;

final class SuQLExpressionTest extends TestCase
{
    public function testCondition(): void
    {
        $this->assertEquals(
            new SuQLExpression(
                '$1 and $2',
                [
                    (new SuQLCondition(new SuQLFieldName('users', 'id'), '$ > 0'))->setFormat('%t.%n'),
                    (new SuQLCondition(new SuQLFieldName('users', 'id'), '$ < 10'))->setFormat('%t.%n'),
                ]
            ),
            'users.id > 0 and users.id < 10'
        );
    }
}
