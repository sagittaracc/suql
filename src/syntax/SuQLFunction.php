<?php

class SuQLFunction extends RawSuQL implements SuQLFunctionInterface
{
    private $name;

    public static function find($name = null)
    {
        $function = parent::func();
        $function->name = $name;
        return $function;
    }

    public function params()
    {
        return $this;
        // return parent::field("{$this->name}(" . implode(',', func_get_args()) . ")");
    }
}