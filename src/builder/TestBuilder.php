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
    $this->applyModifier($query);

    $selectTemplate = self::SELECT_TEMPLATE;

    $selectTemplate = str_replace('#select#', $this->buildSelect($query), $selectTemplate);
    $selectTemplate = str_replace('#from#'  , $this->buildFrom($query),   $selectTemplate);
    $selectTemplate = str_replace('#join#'  , $this->buildJoin($query),   $selectTemplate);
    $selectTemplate = str_replace('#group#' , $this->buildGroup($query),  $selectTemplate);
    $selectTemplate = str_replace('#where#' , $this->buildWhere($query),  $selectTemplate);
    $selectTemplate = str_replace('#having#', $this->buildHaving($query), $selectTemplate);
    $selectTemplate = str_replace('#order#' , $this->buildOrder($query),  $selectTemplate);
    $selectTemplate = str_replace('#limit#' , $this->buildLimit($query),  $selectTemplate);

    return $selectTemplate;
  }

  private function buildUnionQuery($query)
  {
    return 'union';
  }

  private function composeQuery($query)
  {
    return 'composing';
  }

  public function applyModifier($query)
  {
    $oselect = $this->osuql->getQuery($query);
    foreach ($oselect->getSelect() as $field => $ofield) {
      if ($ofield->hasModifier()) {
        foreach ($ofield->getModifierList() as $name => $params) {
          $modifier_handler = "mod_$name";
          if (method_exists(SQLModifier::class, $modifier_handler))
            SQLModifier::$modifier_handler($ofield, $params);
        }
      }
    }
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
