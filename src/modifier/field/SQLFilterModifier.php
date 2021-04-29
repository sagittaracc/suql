<?php

namespace suql\modifier\field;

// TODO: Должно наследоваться от SQLWhereModifier
class SQLFilterModifier
{
    public static function mod_filter($ofield, $params)
    {
        $filterFunction = $params[0];
        $filterValue = $params[1];

        if (!is_null($filterValue)) {
            $whereModifier = "mod_$filterFunction";
            if (method_exists(SQLWhereModifier::class, $whereModifier)) {
                SQLWhereModifier::$whereModifier($ofield, [$filterValue], true);
            }
        }
    }
}
