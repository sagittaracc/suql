<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\builder\MySQLBuilder;
use suql\core\Obj;
use suql\core\Scheme;

class SuQLTest extends TestCase
{
    protected $osuql;

    protected function setUp(): void
    {
        $scheme = new Scheme();

        $scheme->addTableList([
            'users' => 'u',
            'user_group' => 'ug',
            'groups' => 'g',
        ]);

        $scheme->rel('{{u}}', '{{ug}}', '{{u}}.id = {{ug}}.user_id');
        $scheme->rel('{{ug}}', '{{g}}', '{{ug}}.group_id = {{g}}.id');

        $builder = new MySQLBuilder();

        $this->osuql = new Obj($scheme, $builder);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }

    // TODO: Удалить и сделать хотя бы один реальный тест
    public function testEmpty(): void
    {
        $this->assertTrue(true);
    }
}