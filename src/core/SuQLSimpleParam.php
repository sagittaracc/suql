<?php

namespace suql\core;

class SuQLSimpleParam extends SuQLParam
{
    public function getPlaceholder()
    {
        return $this->getPlaceholderList()[0];
    }
}