<?php

namespace suql\frontend\html\element\attribute;

class Attribute
{
    public static function stringValue(string $string)
    {
        return '"'.$string.'"';
    }
}