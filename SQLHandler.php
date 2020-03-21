<?php
class SQLHandler
{
  protected function buildField($table, $field, $alias = null) {
    return ($table ? "$table." : '') . $field . ($alias ? " as $alias" : '');
  }
}
