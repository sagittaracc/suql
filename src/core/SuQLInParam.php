<?php

namespace suql\core;

class SuQLInParam extends SuQLParam
{
    public function getPlaceholder()
    {
        return '(' . implode(',', $this->getPlaceholderList()) . ')';
    }
}