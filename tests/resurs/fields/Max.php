<?php

namespace resurs\fields;

use suql\syntax\Field;

class Max extends Field
{
    function __construct($field)
    {
        return parent::__construct($field, ['max']);
    }
}