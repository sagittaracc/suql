<?php
use core\SuQLSpecialSymbols;
use Helper\CArray;

class TestBuilder
{
  private $osuql = null;
  private $sql = [];

  const SELECT_TEMPLATE = "#select##from##join##where##group##having##order##limit#";
  const REGEX_SUB_QUERY = '/{:v:}(?<name>\w+)/msi';

  function __construct($osuql)
  {
    $this->osuql = $osuql;
  }

  public function getSql($queryList)
  {
    if (empty($this->sql)) return null;

    $sqlList = CArray::slice_by_keys($this->sql, $queryList);

    return count($queryList) === 1 && count($sqlList) === 1
            ? reset($sqlList)
            : $sqlList;
  }

  public function run($queryList)
  {
    if (!$this->osuql)
      return;

    $fullQueryList = $this->osuql->getFullQueryList();

    foreach ($fullQueryList as $query) {
      $this->sql[$query] = trim($this->buildQuery($query));
    }

    // foreach ($queryList as $query) {
    //   $this->sql[$query] = $this->composeQuery($query);
    // }
  }

  private function buildQuery($query)
  {
    $queryType = $this->osuql->getQuery($query)->getType();
    $handler = 'build'.ucfirst($queryType).'Query';
    return method_exists($this, $handler)
            ? $this->$handler($query)
            : null;
  }

  private function buildSelectQuery($query)
  {
    return 'select';
  }

  private function buildUnionQuery($query)
  {
    return 'union';
  }

  private function composeQuery($query)
  {
    return 'composing';
  }

  private function prepareQuery($query)
  {

  }

  protected function buildSelect($query)
  {

  }

  protected function buildFrom($query)
  {

  }

  protected function buildJoin($query)
  {

  }

  protected function buildGroup($query)
  {

  }

  protected function buildWhere($query)
  {

  }

  protected function buildHaving($query)
  {

  }

  protected function buildOrder($query)
  {

  }

  protected function buildLimit($query)
  {

  }
}
