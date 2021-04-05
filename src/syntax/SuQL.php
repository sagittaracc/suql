<?php

use core\SuQLObject;
use sagittaracc\ArrayHelper;

abstract class SuQL extends SuQLObject implements SuQLInterface
{
  protected $adapter = 'mysql';
  private $storage = [];
  private $currentModel;

  public function query()
  {
    return str_replace('\\', '_', get_class($this));
  }

  public function table()
  {
    return str_replace('\\', '_', get_class($this));
  }

  public function getRawSql()
  {
    return parent::getSQL([$this->query()]);
  }

  public static function find()
  {
    $instance = new static();
    $instance->currentModel = get_class($instance);

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

  public function field($name, $modifiers = [])
  {
    $currentModel = new $this->currentModel;

    $this->getQuery($this->query())->addField($currentModel->table(), $name);

    foreach ($modifiers as $modifier => $params)
    {
      $this->getQuery($this->query())->getField($currentModel->table(), $name)->addModifier($modifier, $params);
    }

    return $this;
  }

  public function join($model)
  {
    $this->currentModel = $model;
    $relations = $this->relations();

    if (!isset($relations[$model]))
    {
      foreach ($this->storage as $models)
      {
        $table = $models->table();
        $relations = $models->relations();
        if (isset($relations[$model]))
        {
          $relation = $relations[$model];
          break;
        }
      }
    }
    else
    {
      $table = $this->table();
      $relation = $relations[$model];
    }

    foreach ($relation as $a => $b) ;

    $this->rel($table, (new $model)->table(), "$table.$a = " . (new $model)->table() . ".$b");
    $this->getQuery($this->query())->addJoin('inner', (new $model)->table());

    $this->storage[] = new $model;

    return $this;
  }
}
