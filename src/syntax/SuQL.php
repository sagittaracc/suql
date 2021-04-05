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
    return 'main';
  }

  public function table()
  {
    return 'main';
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

  public function join(string $model)
  {
    $this->currentModel = $model;
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
