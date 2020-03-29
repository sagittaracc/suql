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

  private function buildQuery($query)
  {
    $sqlTemplate = "#select##from##join##where##group##order#";

    $queryObject = $this->getQuery($query);

    $sqlTemplate = str_replace('#select#', $this->buildSelect($queryObject), $sqlTemplate);
    $sqlTemplate = str_replace('#from#'  , $this->buildFrom($queryObject), $sqlTemplate);
    $sqlTemplate = str_replace('#join#'  , $this->buildJoin($queryObject), $sqlTemplate);
    $sqlTemplate = str_replace('#group#' , $this->buildGroup($queryObject), $sqlTemplate);
    $sqlTemplate = str_replace('#where#' , $this->buildWhere($queryObject), $sqlTemplate);
    $sqlTemplate = str_replace('#order#' , $this->buildOrder($queryObject), $sqlTemplate);

    return $sqlTemplate;
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

  private function buildSelect($queryObject) {
    $select = $queryObject['select'];

    if (empty($select))
      return '';

    return 'select ' . implode(', ', array_keys($select));
  }

  private function buildFrom($queryObject) {
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

  private function buildJoin($queryObject) {
    $join = $this->parseJoin($queryObject);

    if (empty($join))
      return '';

    $s = [];
    foreach ($join as $_join) {
      $s[] = "{$_join['type']} join {$_join['table']} on {$_join['on']}";
    }

    return ' ' . implode(' ', $s);
  }

  private function buildGroup($queryObject) {
    $group = $queryObject['group'];
    return !empty($group) ? ' group by ' . implode(', ', $group) : '';
  }

  private function buildWhere($queryObject) {
    $where = $this->parseWhere($queryObject);
    return !empty($where) ? ' where ' . implode(' and ', $where) : '';
  }

  private function buildOrder($queryObject) {
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
