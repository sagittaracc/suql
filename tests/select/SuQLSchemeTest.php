<?php

declare(strict_types=1);

final class SuQLSchemeTest extends SuQLMock
{
    public function testTableList(): void
    {
        $this->assertEquals(
            [
                'users' => 'u',
                'user_group' => 'ug',
                'groups' => 'g',
            ],
            $this->osuql->getScheme()->getTableList()
        );
    }
}