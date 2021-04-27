<?php

class SuQLFunction extends RawSuQL implements SuQLFunctionInterface
{
    protected $name;

    public static function find($name = null)
    {
        $function = parent::func();
        $function->name = $name;
        return $function;
    }

    public function getName()
    {
        return $this->name;
    }

    public function params()
    {
        $this->getQuery($this->query())->addParams(func_get_args());
        return $this;
    }
}