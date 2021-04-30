<?php

use sagittaracc\ArrayHelper;
use suql\syntax\SuQLViewInterface;

abstract class SuQLView extends SuQL implements SuQLViewInterface
{
  function __construct()
  {
    parent::__construct();
  }
  
  public function getType()
  {
    return 'query';
  }

  public static function find()
  {
    $instance = new static();
    $instance->currentModel = get_class($instance);

    $view = $instance->view();

    $queries = ArrayHelper::rename_keys($view->getQueries(), [$instance->query()]);

    $instance->extend($queries);
    $instance->currentQuery = md5($instance->query());
    $instance->addSelect(md5($instance->query()));
    $instance->getQuery(md5($instance->query()))->addFrom($instance->query());

    return $instance;
  }
}
