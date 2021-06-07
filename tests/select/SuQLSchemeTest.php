<?php

declare(strict_types=1);

final class SuQLSchemeTest extends SuQLMock
{
    public function testTableList(): void
    {
        $this->assertEquals(
            [
                'u' => 'users',
                'ug' => 'user_group',
                'g' => 'groups',
            ],
            $this->osuql->getScheme()->getTableList()
        );
    }
}