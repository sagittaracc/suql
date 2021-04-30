<?php

namespace suql\core;

class SuQLBetweenParam extends SuQLParam
{
    public function getPlaceholder()
    {
        return implode(' and ', $this->getPlaceholderList());
    }
}