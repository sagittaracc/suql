<?php

namespace core;

class SuQLLikeParam extends SuQLParam
{
    public function getValue()
    {
        return "%{$this->params[0]}%";
    }
}