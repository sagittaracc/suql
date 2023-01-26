<?php

namespace suql\annotation\attributes;

use Attribute;

#[Attribute]
class Table
{
    function __construct(
        public string $name,
        public null|string $alias = null
    ) {}
}