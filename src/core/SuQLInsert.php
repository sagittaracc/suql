<?php

namespace suql\core;

use sagittaracc\PlaceholderHelper;
use suql\core\interface\InsertQueryInterface;

class SuQLInsert extends SuQLQuery implements InsertQueryInterface
{
  private $table = null;
  private $values = [];

  public function addInto($table)
  {
    $this->table = $table;
  }

  public function addValue($field, $value)
  {
    $this->values[$field] = (new PlaceholderHelper("?"))->bind($value);
  }

  public function addPlaceholder($field, $placeholder)
  {
    $this->values[$field] = $placeholder;
  }

  public function getTable()
  {
    return $this->table;
  }

  public function getFields()
  {
    return implode(',', array_keys($this->values));
  }

  public function getValues()
  {
    return implode(',', array_values($this->values));
  }
}
