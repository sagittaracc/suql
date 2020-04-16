<?php
class SQLBuilder
{
  private $SQLObject = null;
  private $sql = null;

  function __construct($SQLObject)
  {
    $this->SQLObject = $SQLObject;
  }

  public function getSQLObject()
  {
    return $this->SQLObject;
  }

  public function getSql()
  {
    return $this->sql;
  }

  public function run()
  {
    if (!$this->SQLObject)
      return;

    $this->sql = trim($this->buildQuery('main'));
  }

  private function getQuery($query)
  {
    if (isset($this->SQLObject['queries'][$query]))
      return $this->SQLObject['queries'][$query];
    else
      return false;
  }

  private function setQuery($query, $queryObject)
  {
    $this->SQLObject['queries'][$query] = $queryObject;
  }

  private function buildQuery($query)
  {
    $sqlTemplate = "#select##from##join##where##group##having##order#";

    $this->setQuery($query, $this->prepareQuery($query));

    $sqlTemplate = str_replace('#select#', $this->buildSelect($query), $sqlTemplate);
    $sqlTemplate = str_replace('#from#'  , $this->buildFrom($query), $sqlTemplate);
    $sqlTemplate = str_replace('#join#'  , $this->buildJoin($query), $sqlTemplate);
    $sqlTemplate = str_replace('#group#' , $this->buildGroup($query), $sqlTemplate);
    $sqlTemplate = str_replace('#where#' , $this->buildWhere($query), $sqlTemplate);
    $sqlTemplate = str_replace('#having#', $this->buildHaving($query), $sqlTemplate);
    $sqlTemplate = str_replace('#order#' , $this->buildOrder($query), $sqlTemplate);

    return $sqlTemplate;
  }

  private function prepareQuery($query) {
    $queryObject = $this->getQuery($query);

    foreach ($queryObject['select'] as $field => $options) {
      if (empty($options['modifier']))
        continue;

      foreach ($options['modifier'] as $modifier => $params) {
        $modifier_handler = "mod_$modifier";
  			if (method_exists(SQLModifier::class, $modifier_handler))
  				SQLModifier::$modifier_handler($queryObject, $field);
      }
    }

    return $queryObject;
  }

  private function buildSelect($query) {
    $queryObject = $this->getQuery($query);

    $select = $queryObject['select'];

    if (empty($select))
      return '';

    $selectList = [];
    foreach ($select as $fieldOptions) {
      $selectList[] = $fieldOptions['field'] . ($fieldOptions['alias'] ? " as {$fieldOptions['alias']}" : '');
    }

    return 'select ' . implode(', ', $selectList);
  }

  private function buildFrom($query) {
    $queryObject = $this->getQuery($query);

    $from = $queryObject['from'];

    if (empty($from))
      return '';

    if ($this->getQuery($from)) {
      $nestedQuery = $this->buildQuery($from);
      return " from ($nestedQuery) {$from}";
    } else {
      return " from $from";
    }
  }

  private function buildJoin($query) {
    $queryObject = $this->getQuery($query);

    $join = $queryObject['join'];
    $select = $queryObject['select'];

    foreach ($join as &$_join) {
      $_join['on'] = str_replace(array_column($select, 'alias'), array_column($select, 'field'), $_join['on']);
    }
    unset($_join);

    if (empty($join))
      return '';

    $s = [];
    foreach ($join as $_join) {
      $s[] = "{$_join['type']} join {$_join['table']} on {$_join['on']}";
    }

    return ' ' . implode(' ', $s);
  }

  private function buildGroup($query) {
    $queryObject = $this->getQuery($query);

    $group = $queryObject['group'];
    return !empty($group) ? ' group by ' . implode(', ', $group) : '';
  }

  private function buildWhere($query) {
    $queryObject = $this->getQuery($query);

    $where = $queryObject['where'];
    $select = $queryObject['select'];

    foreach ($where as &$_where) {
      $_where = str_replace(array_column($select, 'alias'), array_column($select, 'field'), $_where);
    }
    unset($_where);

    return !empty($where) ? ' where ' . implode(' and ', $where) : '';
  }

  private function buildHaving($query) {
    $queryObject = $this->getQuery($query);

    $having = $queryObject['having'];
    return !empty($having) ? ' having ' . implode(' and ', $having) : '';
  }

  private function buildOrder($query) {
    $queryObject = $this->getQuery($query);

    $order = $queryObject['order'];

    if (empty($order))
      return '';

    $s = [];
    foreach ($order as $_order) {
      $s[] = "{$_order['field']} {$_order['direction']}";
    }

    return ' order by ' . implode(', ', $s);
  }
}
