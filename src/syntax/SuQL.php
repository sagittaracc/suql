<?php

use core\SuQLObject;
use sagittaracc\ArrayHelper;

abstract class SuQL extends SuQLObject implements SuQLQueryInterface
{
  protected $driver = 'mysql';
  protected $joinChain = [];
  protected $currentModel;
  protected $currentQuery;

  use SQLDistinctModifier;

  abstract public function getType();

  public function query()
  {
    return str_replace('\\', '_', get_class($this));
  }

  public function getRawSql()
  {
    return parent::getSQL([$this->currentQuery]);
  }

  public function limit($offset, $limit)
  {
    $this->getQuery($this->query())->addOffset($offset);
    $this->getQuery($this->query())->addLimit($limit);

    return $this;
  }

  public function field($name, $modifiers = [], $visible = true)
  {
    $currentModel = new $this->currentModel;
    $type = $currentModel->getType();

    $this->getQuery($this->currentQuery)->addField($currentModel->$type(), $name, $visible);

    if ($modifiers instanceof Closure)
    {
      $this->getQuery($this->currentQuery)->getField($currentModel->$type(), $name)->addCallbackModifier($modifiers);
    }
    else if (ArrayHelper::isSequential($modifiers))
    {
      foreach ($modifiers as $modifier)
      {
        $this->getQuery($this->currentQuery)->getField($currentModel->$type(), $name)->addModifier($modifier);
      }
    }
    else
    {
      foreach ($modifiers as $modifier => $params)
      {
        $this->getQuery($this->currentQuery)->getField($currentModel->$type(), $name)->addModifier($modifier, $params);
      }
    }

    return $this;
  }

  public function select($fieldList)
  {
    $currentModel = new $this->currentModel;
    $type = $currentModel->getType();

    if (ArrayHelper::isSequential($fieldList))
    {
      foreach ($fieldList as $field)
      {
        $this->getQuery($this->currentQuery)->addField($currentModel->$type(), $field);
      }
    }
    else
    {
      foreach ($fieldList as $field => $alias)
      {
        $this->getQuery($this->currentQuery)->addField($currentModel->$type(), [$field => $alias]);
      }
    }

    return $this;
  }

  public function filter($field, $options)
  {
    $this->field($field, [
      'filter' => $options
    ], false);

    return $this;
  }

  public function raw($field)
  {
    $this->getQuery($this->query())->addField(null, $field);
    return $this;
  }

  public function relations()
  {
    return [];
  }

  public function join($model)
  {
    $this->currentModel = $model;
    $relations = $this->relations();

    if (!isset($relations[$model]))
    {
      foreach ($this->joinChain as $models)
      {
        $type = $models->getType();
        $table = $models->$type();
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
      $type = $this->getType();
      $table = $this->$type();
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
