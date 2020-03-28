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

  private function parseFields($cond, $select)
  {
    $a = array_column($select, 'field');
    $b = array_column($select, 'alias');
    return str_replace($b, $a, $cond);
  }

  private function parseJoin($join, $select)
  {
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

  private function parseWhere($where, $select)
  {
    foreach ($where as &$_where) {
      $_where = $this->parseFields($_where, $select);
    }
    unset($_where);

    return $where;
  }

  private function buildSelect($select) {
    if (empty($select))
      return '';

    return 'select ' . implode(', ', array_keys($select));
  }

  private function buildFrom($from) {
    return $from ? " from $from" : '';
  }

  private function buildJoin($join) {
    if (empty($join))
      return '';

    $s = [];
    foreach ($join as $_join) {
      $s[] = "{$_join['type']} join {$_join['table']} on {$_join['on']}";
    }

    return ' ' . implode(' ', $s);
  }

  private function buildGroup($group) {
    return !empty($group) ? ' group by ' . implode(', ', $group) : '';
  }

  private function buildWhere($where) {
    return !empty($where) ? ' where ' . implode(' and ', $where) : '';
  }

  private function buildOrder($order) {
    if (empty($order))
      return '';

    $s = [];
    foreach ($order as $_order) {
      $s[] = "{$_order['field']} {$_order['direction']}";
    }

    return ' order by ' . implode(', ', $s);
  }

  private function buildQuery($query)
  {
    $sqlTemplate = "#select##from##join##where##group##order#";

    $queryObject = $this->SQLObject['queries'][$query];

    $sqlTemplate = str_replace('#select#', $this->buildSelect($queryObject['select']), $sqlTemplate);
    if (isset($this->SQLObject['queries'][$queryObject['from']]))
      $sqlTemplate = str_replace('#from#', ' from (' . $this->buildQuery($queryObject['from']) . ') ' . $queryObject['from'], $sqlTemplate);
    else
      $sqlTemplate = str_replace('#from#', $this->buildFrom($queryObject['from']), $sqlTemplate);
    $sqlTemplate = str_replace('#join#', $this->buildJoin($this->parseJoin($queryObject['join'], $queryObject['select'])), $sqlTemplate);
    $sqlTemplate = str_replace('#group#', $this->buildGroup($queryObject['group']), $sqlTemplate);
    $sqlTemplate = str_replace('#where#', $this->buildWhere($this->parseWhere($queryObject['where'], $queryObject['select'])), $sqlTemplate);
    return str_replace('#order#', $this->buildOrder($queryObject['order']), $sqlTemplate);
  }
}
