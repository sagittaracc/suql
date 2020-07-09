<?php
use Helper\CRegExp;

class SuQLRegExp extends CRegExp {
  function __construct($regex, $flags = '') {
    parent::registerSequence('v', [SuQLSpecialSymbols::$prefix_declare_variable]);
    parent::registerSequence('f', [SuQLSpecialSymbols::$prefix_declare_field_alias]);
    parent::__construct($regex, $flags);
  }
}
