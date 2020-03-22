<?php
require('SQLBuilder.php');

class SuQLBuilder extends SQLBuilder
{
  private $SuQLObject = null;
  private $sql = null;

  function __construct($SuQLObject)
  {
    $this->SuQLObject = $SuQLObject;
  }

  public function getSql()
  {
    return $this->sql;
  }

  public function run()
  {
    if (!$this->SuQLObject)
      return;

    $this->sql = trim($this->buildQuery('main'));
  }

  private function parseJoinFields($on, $select)
  {
    return str_replace(array_keys($select), array_values($select), $on);
  }

  private function parseJoin($join, $select)
  {
    foreach ($join as &$_join) {
      $on = $_join['on'];

      $on = $this->parseJoinFields($on, $select);

      if (count(explode('<-->', $on)) === 2) {
        $_join['type'] = 'inner';
        $_join['on'] = implode(' = ', explode('<-->', $on));
      } else if (count(explode('-->', $on)) === 2) {
        $_join['type'] = 'right';
        $_join['on'] = implode(' = ', explode('-->', $on));
      } else if (count(explode('<--', $on)) === 2) {
        $_join['type'] = 'left';
        $_join['on'] = implode(' = ', explode('<--', $on));
      } else {

      }
    }
    unset($_join);

    return $join;
  }

  protected function buildSelect($select)
  {
    foreach ($select as $alias => $params) {
      $select[$alias] = ($params['function'] ? "{$params['function']}(" : '') . $params['field'] . ($params['function'] ? ')' : '');
    }
    return $select;
  }

  private function buildQuery($query)
  {
    $sqlTemplate = "
      #select#
      #from#
      #join#
      #group#
      #where#
      #order#
    ";

    $queryObject = $this->SuQLObject['queries'][$query];
    $queryObject['select'] = $this->buildSelect($queryObject['select']);

    $sqlTemplate = str_replace('#select#', parent::buildSelect($queryObject['select']), $sqlTemplate);
    if (isset($this->SuQLObject['queries'][$queryObject['from']]))
      $sqlTemplate = str_replace('#from#', 'from (' . $this->buildQuery($queryObject['from']) . ') ' . $queryObject['from'], $sqlTemplate);
    else
      $sqlTemplate = str_replace('#from#', $this->buildFrom($queryObject['from']), $sqlTemplate);
    $sqlTemplate = str_replace('#join#', $this->buildJoin($this->parseJoin($queryObject['join'], $queryObject['select'])), $sqlTemplate);
    $sqlTemplate = str_replace('#group#', $this->buildGroup($queryObject['group']), $sqlTemplate);
    $sqlTemplate = str_replace('#where#', $this->buildWhere($queryObject['where']), $sqlTemplate);
    return str_replace('#order#', $this->buildOrder($queryObject['order']), $sqlTemplate);
  }
}
