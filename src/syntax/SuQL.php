<?php

use core\SuQLObject;
use sagittaracc\ArrayHelper;

abstract class SuQL extends SuQLObject implements SuQLInterface
{
  protected $driver = 'mysql';
  private $joinChain = [];
  private $currentModel;
  private $isView = false;

  use SQLDistinctModifier;

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

    if (method_exists($instance, 'view'))
    {
      $instance->isView = true;
      $view = $instance->view();

      if ($view->isView)
      {
        $instance->addSelect($instance->query());
        $instance->getQuery($instance->query())->addFrom($view->query());
        $instance->extend($view->getQueries());
      }
      else
      {
        $queries = ArrayHelper::rename_keys($view->getQueries(), [$instance->query()]);
        $instance->extend($queries);
      }
    }
    else
    {
      $instance->addSelect($instance->query());
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

  public function field($name, $modifiers = [], $visible = true)
  {
    $currentModel = new $this->currentModel;

    $this->getQuery($this->query())->addField($currentModel->table(), $name, $visible);

    if (ArrayHelper::isSequential($modifiers))
    {
      foreach ($modifiers as $modifier)
      {
        $this->getQuery($this->query())->getField($currentModel->table(), $name)->addModifier($modifier);
      }
    }
    else
    {
      foreach ($modifiers as $modifier => $params)
      {
        $this->getQuery($this->query())->getField($currentModel->table(), $name)->addModifier($modifier, $params);
      }
    }

    return $this;
  }

  public function join($model)
  {
    $this->currentModel = $model;
    $relations = $this->relations();

    if (!isset($relations[$model]))
    {
      foreach ($this->joinChain as $models)
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

    $self = new $model;

    if (method_exists($self, 'view'))
    {
      $this->temp_rel($table, $self->query(), "$table.$a = " . $self->query() . ".$b");
      $this->getQuery($this->query())->addJoin('inner', $self->query());
      $view = $self->view();

      $queries = ArrayHelper::rename_keys($view->getQueries(), [$self->query()]);
      $this->extend($queries);
    }
    else
    {
      $this->rel($table, $self->table(), "$table.$a = " . $self->table() . ".$b");
      $this->getQuery($this->query())->addJoin('inner', $self->table());
    }

    $this->joinChain[] = $self;

    return $this;
  }
}
