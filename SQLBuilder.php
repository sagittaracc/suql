<?php
class SQLBuilder
{
  protected function buildSelect($select) {
    if (empty($select))
      return '';

    $s = [];

    foreach ($select as $alias => $field) {
      $s[] = $field . ($alias ? " as $alias" : '');
    }

    return 'select ' . implode(', ', $s);
  }

  protected function buildFrom($from) {
    return $from ? "from $from" : '';
  }

  protected function buildJoin($join) {
    if (empty($join))
      return '';

    $s = [];
    foreach ($join as $_join) {
      $s[] = "{$_join['type']} join {$_join['table']} on {$_join['on']}";
    }

    return implode(' ', $s);
  }

  protected function buildGroup($group) {
    return !empty($group) ? 'group by ' . implode(', ', $group) : '';
  }

  protected function buildWhere($where) {
    return !empty($where) ? 'having ' . implode(' and ', $where) : '';
  }

  protected function buildOrder($order) {
    if (empty($order))
      return '';

    $s = [];
    foreach ($order as $_order) {
      $s[] = "{$_order['field']} {$_order['direction']}";
    }

    return 'order by ' . implode(', ', $s);
  }
}
