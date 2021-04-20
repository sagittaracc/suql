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
    return parent::getSQL([$this->currentQuery]);
  }

  public function __toString()
  {
    return $this->getRawSql();
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

  public function limit($offset, $limit)
  {
    $this->getQuery($this->query())->addOffset($offset);
    $this->getQuery($this->query())->addLimit($limit);

    return $this;
  }

  public function field($name, $modifiers = [], $visible = true)
  {
    $currentModel = new $this->currentModel;

    $this->getQuery($this->currentQuery)->addField($currentModel->table(), $name, $visible);

    if ($modifiers instanceof Closure)
    {
      $this->getQuery($this->currentQuery)->getField($currentModel->table(), $name)->addCallbackModifier($modifiers);
    }
    else if (ArrayHelper::isSequential($modifiers))
    {
      foreach ($modifiers as $modifier)
      {
        $this->getQuery($this->currentQuery)->getField($currentModel->table(), $name)->addModifier($modifier);
      }
    }
    else
    {
      foreach ($modifiers as $modifier => $params)
      {
        $this->getQuery($this->currentQuery)->getField($currentModel->table(), $name)->addModifier($modifier, $params);
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
