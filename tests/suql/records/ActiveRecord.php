<?php

namespace test\suql\records;

use suql\syntax\SuQL;
use test\suql\modifiers\CustomModifier;

abstract class ActiveRecord extends SuQL
{
    protected static $schemeClass = 'test\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    protected function modifierList()
    {
        return array_merge(
            parent::modifierList(),
            [
                CustomModifier::class,
            ]
        );
    }

    public function getDb()
    {
        return null;
    }
}
