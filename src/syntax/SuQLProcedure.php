<?php

class SuQLProcedure extends SuQLFunction implements SuQLProcedureInterface
{
    public static function find($name = null)
    {
        $procedure = parent::proc();
        $procedure->name = $name;
        return $procedure;
    }
}
