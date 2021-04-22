<?php

namespace core;

class SuQLPlaceholder
{
    private $placeholder;

    function __construct($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    public function getPlaceholder()
    {
        return ":$this->placeholder";
    }

    public function __toString()
    {
        return $this->getPlaceholder();
    }
}