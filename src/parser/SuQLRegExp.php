<?php
use Helper\CRegExp;

class SuQLRegExp extends CRegExp {
    protected $sequenceList = [
        '{:v:}' => '[@]',   // prefix for declaring a variable
        '{:f:}' => '[@]',   // prefix for declaring a field alias
    ];
}
