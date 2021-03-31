<?php
use core\SuQLObject;

class SuQL extends SuQLObject
{
  public static function find()
  {
    $instance = new static;
    $instance->setAdapter('mysql');

    if (method_exists($instance, 'tableName')) {
      $instance->addSelect('main');
      $instance->getQuery('main')->addFrom($instance->tableName());
      $instance->getQuery('main')->addField($instance->tableName(), 'id');
    }
    else {
      $tableView = $instance->tableView();
      foreach ($tableView as $alias => $view) ;
      $table = $view->getQuery('main')->getFrom();
      $instance->addSelect($alias);
      $instance->getQuery($alias)->addFrom($table);
      $instance->getQuery($alias)->addField($table, 'id');
      $instance->addSelect('main');
      $instance->getQuery('main')->addFrom($alias);
    }

    return $instance;
  }
}
