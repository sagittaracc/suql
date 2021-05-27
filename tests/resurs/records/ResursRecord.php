<?php

namespace resurs\records;

use DbContainer;
use resurs\modifiers\ResursModifier;
use suql\syntax\SuQL;

abstract class ResursRecord extends SuQL
{
    protected static $schemeClass = 'resurs\\schema\\AppSchema';
    protected static $sqlDriver = 'mysql';

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
        return DbContainer::get('ResursDb');
    }
}