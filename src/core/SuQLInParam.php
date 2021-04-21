<?php

namespace core;

class SuQLInParam extends SuQLParam
{
    public function getValue()
    {
        return '(' . implode(',', $this->params) . ')';
    }
}