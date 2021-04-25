<?php

namespace suql\core;

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
}