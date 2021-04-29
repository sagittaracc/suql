<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLCondition;
use suql\core\SuQLFieldName;

final class SuQLConditionTest extends TestCase
{
    public function testCondition(): void
    {
        $this->assertEquals(
            new SuQLCondition(
                new SuQLFieldName('users', 'id'),
                '$ > 0',
                '%t.%n'
            ),
            'users.id > 0'
        );
    }
}
