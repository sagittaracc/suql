<?php

namespace app\records;

use suql\syntax\SuQL;
use app\modifiers\CustomModifier;
use suql\db\Container;

abstract class ActiveRecord extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
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
        return Container::get('connection');
    }
}
