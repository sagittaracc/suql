<?php
function suql_autoloader($class) {
  include 'src/' . $class . '.php';
}

spl_autoload_register('suql_autoloader');
