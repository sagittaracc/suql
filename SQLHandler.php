<?php
class SQLHandler
{
  protected function buildField($name, $alias = null) {
    return $name . ( $alias ? " as $alias" : '');
  }
}
