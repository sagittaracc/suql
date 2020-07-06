<?php
use Helper\CRegExp;

class SuQLRegExp extends CRegExp {
    public static $prefix_declare_variable = ['@'];
    public static $prefix_declare_field_alias = ['@'];

    function __construct($regex, $flags = '') {
        parent::registerSequence('v', self::$prefix_declare_variable);
        parent::registerSequence('f', self::$prefix_declare_field_alias);
        parent::__construct($regex, $flags);
    }
}
