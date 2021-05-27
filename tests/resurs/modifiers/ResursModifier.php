<?php

namespace resurs\modifiers;

use suql\core\FieldName;
use suql\modifier\field\SQLCaseModifier;

class ResursModifier extends SQLCaseModifier
{
    public static function mod_vt_unit($ofield, $params)
    {
        $table = $ofield->getTable();
        $field = $ofield->getField();
        $fieldName = new FieldName($table, $field);
        
        self::parse([
            [
                [$fieldName, "$ = 'Temperature'"],
                'C',
            ],
            [
                [$fieldName, "$ = 'Humidity'"],
                '%',
            ],
        ], $ofield, $params);
    }
}