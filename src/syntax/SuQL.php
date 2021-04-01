<?php

use core\SuQLObject;
use sagittaracc\ArrayHelper;

class SuQL extends SuQLObject
{
  protected $adapter = 'mysql';
  private $storage = [];

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

  public function select(array $fields)
  {
    if (ArrayHelper::isSequential($fields))
    {
      foreach ($fields as $field)
      {
        $this->getQuery($this->query())->addField(
          method_exists($this, 'table') ? $this->table() : $this->view()->query(),
          $field
        );
      }
    }
    else
    {
      foreach ($fields as $field => $alias)
      {
        $this->getQuery($this->query())->addField(
          method_exists($this, 'table') ? $this->table() : $this->view()->query(),
          [$field => $alias]
        );
      }
    }

    return $this;
  }

  public function join(string $model)
  {
    $links = $this->link();

    if (!isset($links[$model]))
    {
      foreach ($this->storage as $models)
      {
        $table = $models->table();
        $links = $models->link();
        if (isset($links[$model]))
        {
          $link = $links[$model];
          break;
        }
      }
    }
    else
    {
      $table = $this->table();
      $link = $links[$model];
    }

    foreach ($link as $a => $b) ;

    $this->rel($table, (new $model)->table(), "$table.$a = " . (new $model)->table() . ".$b");
    $this->getQuery($this->query())->addJoin('inner', (new $model)->table());

    $this->storage[] = new $model;

    return $this;
  }
}
