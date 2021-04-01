<?php
use core\SuQLObject;

class SuQL extends SuQLObject
{
  protected $adapter = 'mysql';

  public function getRawSql()
  {
    return parent::getSQL([$this->query()]);
  }

  public static function find()
  {
    $instance = new static();

    $instance->addSelect($instance->query());
    if (method_exists($instance, 'view'))
    {
      $view = $instance->view();
      $instance->extend($view->getQueries());
      $instance->getQuery($instance->query())->addFrom($view->query());
    }
    else
    {
      $instance->getQuery($instance->query())->addFrom($instance->table());
    }

    return $instance;
  }

  public function select($fields)
  {
    foreach ($fields as $field)
    {
      $this->getQuery($this->query())->addField(
        method_exists($this, 'table') ? $this->table() : $this->view()->query(),
        $field
      );
    }

    return $this;
  }
}
