<?php

class SuQLFunction extends RawSuQL implements SuQLFunctionInterface
{
    public static function find($name = null)
    {
        $function = parent::func($name);
        return $function;
    }

    public function params()
    {
        $this->getQuery($this->query())->addParams(func_get_args());
        return $this;
    }
}