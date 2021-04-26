<?php

/**
 * SuQLFunction::find('getSomething')->params(<paramList>)
 */

class SuQLFunction extends RawSuQL implements SuQLFunctionInterface
{
    private $name;

    public static function find($name = null)
    {
        $function = parent::find();
        $function->name = $name;
        return $function;
    }

    public function params()
    {
        return parent::field("{$this->name}(" . implode(',', func_get_args()) . ")");
    }
}