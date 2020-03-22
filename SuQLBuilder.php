<?php
require('SQLBuilder.php');

class SuQLBuilder extends SQLBuilder
{
  const LEFT_JOIN = '<--';
  const RIGHT_JOIN = '-->';
  const INNER_JOIN = '<-->';

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
    $a = array_column($select, 'field');
    $b = array_column($select, 'alias');
    return str_replace($b, $a, $on);
  }

  private function parseJoin($join, $select)
  {
    foreach ($join as &$_join) {
      $on = $_join['on'];

      $on = $this->parseJoinFields($on, $select);

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

  protected function buildSelect($select)
  {
    // foreach ($select as $field => $params) {
    //   if ($params['function']) {
    //     $select["{$params['function']}(" . $field . ')'] = $params['alias'];
    //     unset($select[$field]);
    //   } else {
    //     $select[$field] = $params['alias'];
    //   }
    // }
    // return $select;
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
