<?php

use suql\core\SuQLObject;
use sagittaracc\ArrayHelper;
use suql\core\SuQLModifier;
use suql\core\SuQLScheme;
use suql\builder\SQLDriver;
use suql\modifier\query\SQLDistinctModifier;

abstract class SuQL extends SuQLObject implements SuQLQueryInterface
{
  protected $dbms = 'mysql';
  protected $joinChain = [];
  protected $currentModel;
  protected $currentQuery;

  use SQLDistinctModifier;

  abstract public function getType();

  function __construct()
  {
    $scheme = new SuQLScheme();
    $driver = new SQLDriver($this->dbms);
    parent::__construct($scheme, $driver);
  }

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

    foreach ($fieldList as $key => $value)
    {
      if ($value instanceof SuQLModifier)
      {
        $this->getQuery($this->currentQuery)->addField($currentModel->$type(), $value->getField());
        $this->getQuery($this->currentQuery)->getField($currentModel->$type(), $value->getField())->addModifier($value->getModifier(), $value->getParams());
      }
      else if (is_int($key))
      {
        $field = $value;
        $this->getQuery($this->currentQuery)->addField($currentModel->$type(), $field);
      }
      else if (is_string($key))
      {
        $field = $key;
        $alias = $value;
        $this->getQuery($this->currentQuery)->addField($currentModel->$type(), [$field => $alias]);
      }
      else
      {

      }
    }

    return $this;
  }

  public function orderBy($fieldList)
  {
    foreach ($fieldList as $field => $direction)
    {
      $this->field($field, [
        $direction
      ]);
    }

    return $this;
  }

  public function groupBy($field)
  {
    $this->field($field, [
      'group'
    ]);

    return $this;
  }

  public function countBy($field)
  {
    $this->field($field, [
      'count'
    ]);

    return $this;
  }

  public function max($field)
  {
    $this->field($field, ['max']);

    return $this;
  }

  public function where($options)
  {
    if (is_string($options))
    {
      $rawWhere = $options;
      $this->getQuery($this->currentQuery)->addWhere($rawWhere);
    }
    else if (is_array($options))
    {
      if (ArrayHelper::isSequential($options))
      {
        $field = $options[0];
        $modifier = $options[1];
        $params = $options[2];

        $this->field($field, [
          $modifier => $params
        ], false);
      }
      else
      {
        foreach ($options as $field => $value)
        {
          $this->field($field, [
            'equal' => [$value]
          ], false);
        }
      }
    }
    else
    {

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
      $this->scheme->temp_rel($table, $self->query(), "$table.$a = " . $self->query() . ".$b");
      $this->getQuery($this->query())->addJoin('inner', $self->query());
      $view = $self->view();

      $queries = ArrayHelper::rename_keys($view->getQueries(), [$self->query()]);
      $this->extend($queries);
    }
    else
    {
      $this->scheme->rel($table, $self->table(), "$table.$a = " . $self->table() . ".$b");
      $this->getQuery($this->query())->addJoin('inner', $self->table());
    }

    $this->joinChain[] = $self;

    return $this;
  }
}
