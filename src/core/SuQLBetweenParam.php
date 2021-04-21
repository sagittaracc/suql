<?php

namespace core;

class SuQLBetweenParam extends SuQLParam
{
    public function getValue()
    {
        return "{$this->params[0]} and {$this->params[1]}";
    }

    public function getPlaceholder()
    {
        $placeholder = parent::getPlaceholder();
        return ':ph_' . md5($placeholder . '1') . ' and ' . ':ph_' . md5($placeholder . '2');
    }
}