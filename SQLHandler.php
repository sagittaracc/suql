<?php
class SQLHandler
{
  protected function buildField($table, $field, $alias = null, $func = null) {
    $s_field = ($table ? "$table." : '') . $field;
    return ($func ? "$func(" : '') . $s_field . ($func ? ')' : '') . ($alias ? " as $alias" : '');
  }

  protected function buildOrderExpression($table, $field, $direction) {
    return $this->buildField($table, $field) . " $direction";
  }
}
