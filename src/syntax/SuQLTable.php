<?php

use sagittaracc\ArrayHelper;

abstract class SuQLTable extends SuQL implements SuQLTableInterface
{
  function __construct($data = [])
  {
    if (!empty($data))
    {
      $this->insert($data);
    }
  }

  public function getType()
  {
    return 'table';
  }

  public function table()
  {
    return str_replace('\\', '_', get_class($this));
  }

  public static function find()
  {
    $instance = new static();
    $instance->currentModel = get_class($instance);

    $instance->currentQuery = $instance->query();
    $instance->addSelect($instance->query());
    $instance->getQuery($instance->query())->addFrom($instance->table());

    return $instance;
  }

  public function insert($values)
  {
    $this->addInsert($this->currentQuery);
    $this->getQuery($this->currentQuery)->addInto($this->table());

    if (ArrayHelper::isSequential($values))
    {
      foreach ($values as $field)
      {
        $this->getQuery($this->currentQuery)->addPlaceholder($field, ":$field");
      }
    }
    else
    {
      foreach ($values as $field => $value)
      {
        $this->getQuery($this->currentQuery)->addValue($field, $value);
      }
    }

    return $this;
  }
}
