<?php

namespace core;

class SuQLBetweenParam extends SuQLParam
{
    public function getPlaceholder()
    {
        return implode(' and ', $this->getPlaceholderList());
    }

    public function getPlaceholderList()
    {
        $placeholder = parent::getPlaceholderList()[0];
        return [
            $this->params[0] instanceof SuQLPlaceholder ? $this->params[0]->getPlaceholder() : ':ph_' . md5($placeholder . '0'),
            $this->params[1] instanceof SuQLPlaceholder ? $this->params[1]->getPlaceholder() : ':ph_' . md5($placeholder . '1'),
        ];
    }

    public function isValuable()
    {
        return !is_null($this->params[0]) && !is_null($this->params[1]);
    }
}