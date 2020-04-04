<?php
class SQLBuilder
{
  const LEFT_JOIN = '<--';
  const RIGHT_JOIN = '-->';
  const INNER_JOIN = '<-->';

  private $SQLObject = null;
  private $sql = null;

  function __construct($SQLObject)
  {
    $this->SQLObject = $SQLObject;
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

    $select = array_keys($queryObject['select']);

    if (empty($select))
      return '';

    foreach ($select as &$field) {
      $field = str_replace('@', ' as ', $field);
    }
    unset($field);

    return 'select ' . implode(', ', $select);
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

    $join = $this->parseJoin($queryObject);

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

    $where = $this->parseWhere($queryObject);
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

  private function parseFields($cond, $select)
  {
    $a = array_column($select, 'field');
    $b = array_column($select, 'alias');
    return str_replace($b, $a, $cond);
  }

  private function parseJoin($queryObject)
  {
    $join = $queryObject['join'];
    $select = $queryObject['select'];

    foreach ($join as &$_join) {
      $on = $_join['on'];

      $on = $this->parseFields($on, $select);

      if (count(explode(self::INNER_JOIN, $on)) === 2) {
        $_join['type'] = 'inner';
        $_join['on'] = implode(' = ', explode(self::INNER_JOIN, $on));
      } else if (count(explode(self::RIGHT_JOIN, $on)) === 2) {
        $_join['type'] = 'right';
        $_join['on'] = implode(' = ', explode(self::RIGHT_JOIN, $on));
      } else if (count(explode(self::LEFT_JOIN, $on)) === 2) {
        $_join['type'] = 'left';
        $_join['on'] = implode(' = ', explode(self::LEFT_JOIN, $on));
      } else {

      }
    }
    unset($_join);

    return $join;
  }

  private function parseWhere($queryObject)
  {
    $where = $queryObject['where'];
    $select = $queryObject['select'];

    foreach ($where as &$_where) {
      $_where = $this->parseFields($_where, $select);
    }
    unset($_where);

    return $where;
  }
}
