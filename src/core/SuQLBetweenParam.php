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
        return implode(' and ', $this->getPlaceholderList());
    }

    public function getPlaceholderList()
    {
        $placeholder = parent::getPlaceholderList()[0];
        return [
            ':ph_' . md5($placeholder . '0'),
            ':ph_' . md5($placeholder . '1')
        ];
    }
}