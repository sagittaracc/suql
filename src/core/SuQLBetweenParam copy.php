<?php

namespace core;

class SuQLBetweenParam extends SuQLParam
{
    public function getValue()
    {
        return "{$this->params[0]} and {$this->params[1]}";
    }
}