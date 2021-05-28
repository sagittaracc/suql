<?php

namespace resurs\records;

use suql\db\Container;
use resurs\modifiers\ResursModifier;
use suql\builder\MySQLBuilder;
use suql\syntax\SuQL;

abstract class ResursRecord extends SuQL
{
    protected static $schemeClass = 'resurs\\schema\\AppSchema';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    protected function modifierList()
    {
        return array_merge(
            parent::modifierList(),
            [
                ResursModifier::class,
            ]
        );
    }

    public function getDb()
    {
        return Container::get('ResursDb');
    }
}
