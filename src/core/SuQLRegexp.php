<?php
use core\SuQLSpecialSymbols;
use sagittaracc\Regexp;

class SuQLRegexp extends Regexp {
  function __construct($regex, $flags = '') {
    parent::registerSequence('v', [SuQLSpecialSymbols::$prefix_declare_variable]);
    parent::registerSequence('f', [SuQLSpecialSymbols::$prefix_declare_field_alias]);
    parent::registerSequence('c', [SuQLSpecialSymbols::$prefix_declare_command]);
    parent::registerSequence('p', [SuQLSpecialSymbols::$prefix_declare_command, SuQLSpecialSymbols::$prefix_declare_variable]);
    parent::__construct($regex, $flags);
  }
}
