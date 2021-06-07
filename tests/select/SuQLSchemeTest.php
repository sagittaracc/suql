<?php

declare(strict_types=1);

final class SuQLSchemeTest extends SuQLMock
{
    public function testTableList(): void
    {
        $this->assertEquals(
            [
                'users' => 'users',
                'ug' => 'user_group',
                'groups' => 'groups',
            ],
            $this->osuql->getScheme()->getTableList()
        );
    }
}