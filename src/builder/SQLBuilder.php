<?php

namespace suql\builder;

use suql\core\SuQLName;
use sagittaracc\ArrayHelper;
use suql\core\InsertQueryInterface;
use suql\core\SelectQueryInterface;
use suql\core\UnionQueryInterface;

class SQLBuilder
{
  private $osuql = null;
  private $sql = [];

  const SELECT_TEMPLATE = "{select}{from}{join}{where}{group}{having}{order}{limit}";
  const INSERT_TEMPLATE = "insert into {table} ({fields}) values ({values})";

  function __construct($osuql)
  {
    $this->osuql = $osuql;
  }

  public function getSql($queryList)
  {
    if (empty($this->sql)) return null;

    $sqlList = ArrayHelper::slice_by_keys($this->sql, $queryList);

    return count($queryList) === 1 && count($sqlList) === 1
            ? reset($sqlList)
            : $sqlList;
  }

  public function run($queryList)
  {
    if (!$this->osuql)
      return;

    $fullQueryList = $this->osuql->getFullQueryList();
    if (empty($fullQueryList))
      return;

    foreach ($fullQueryList as $query) {
      $this->sql[$query] = trim($this->buildQuery($query));
    }

    foreach ($queryList as $query) {
      $this->sql[$query] = $this->composeQuery($query);
    }
  }

  private function buildQuery($query)
  {
    $osuql = $this->osuql->getQuery($query);

    if ($this->osuql instanceof \SuQLProcedure)
    {
      return $this->buildStoredProcedure($query);
    }
    else if ($osuql instanceof SelectQueryInterface)
    {
      return $this->buildSelectQuery($query);
    }
    else if ($osuql instanceof InsertQueryInterface)
    {
      return $this->buildInsertQuery($query);
    }
    else if ($osuql instanceof UnionQueryInterface)
    {
      return $this->buildUnionQuery($query);
    }
    else
    {
      return null;
    }
  }

  private function buildStoredProcedure($query)
  {
    return str_replace('select', 'call', $this->buildSelectQuery($query));
  }

  private function buildSelectQuery($query)
  {
    $this->applyModifier($query);

    $selectTemplate = self::SELECT_TEMPLATE;

    $selectTemplate = str_replace('{select}', $this->buildSelect($query), $selectTemplate);
    $selectTemplate = str_replace('{from}'  , $this->buildFrom($query),   $selectTemplate);
    $selectTemplate = str_replace('{join}'  , $this->buildJoin($query),   $selectTemplate);
    $selectTemplate = str_replace('{group}' , $this->buildGroup($query),  $selectTemplate);
    $selectTemplate = str_replace('{where}' , $this->buildWhere($query),  $selectTemplate);
    $selectTemplate = str_replace('{having}', $this->buildHaving($query), $selectTemplate);
    $selectTemplate = str_replace('{order}' , $this->buildOrder($query),  $selectTemplate);
    $selectTemplate = str_replace('{limit}' , $this->buildLimit($query),  $selectTemplate);

    return $selectTemplate;
  }

  private function buildUnionQuery($query)
  {
    return $this->osuql->getQuery($query)->getSuQL();
  }

  private function buildInsertQuery($query)
  {
    $insertTemplate = self::INSERT_TEMPLATE;

    $insertTemplate = str_replace('{table}', $this->osuql->getQuery($query)->getTable(), $insertTemplate);
    $insertTemplate = str_replace('{fields}', $this->osuql->getQuery($query)->getFields(), $insertTemplate);
    $insertTemplate = str_replace('{values}', $this->osuql->getQuery($query)->getValues(), $insertTemplate);

    return $insertTemplate;
  }

  private function composeQuery($query)
  {
    if (!isset($this->sql[$query]))
      return '';
    $suql = $this->sql[$query];

    preg_match_all('/@(?<name>\w+)/msi', $suql, $subQueries);

    if (empty($subQueries['name']))
      return $suql;
    else {
      foreach ($subQueries['name'] as $subQuery)
        $suql = str_replace("@$subQuery", '('.$this->composeQuery($subQuery).')', $suql);

      return $suql;
    }
  }

  public function applyModifier($query)
  {
    $oselect = $this->osuql->getQuery($query);

    foreach ($oselect->getSelect() as $field => $ofield) {
      if ($ofield->hasModifier()) {
        foreach ($ofield->getModifierList() as $name => $params) {
          if ($name === 'callback' && $params instanceof \Closure) {
            $params($ofield);
          }
          else {
            $modifierHandler = "mod_$name";
            $modifierClass = $this->osuql->getModifierClass($modifierHandler);
            if ($modifierClass)
            {
              $modifierClass::$modifierHandler($ofield, $params);
            }
          }
        }
      }
    }
  }

  protected function buildSelect($query)
  {
    $oselect = $this->osuql->getQuery($query);

    $selectList = [];
    foreach ($oselect->getSelect() as $field => $ofield) {
      if ($ofield->visible()) {
        $fieldName = new SuQLName($ofield->getField(), $ofield->getAlias());
        $selectList[] = $fieldName->format('%n as %a');
      }
    }

    $selectList = empty($selectList) ? '*' : implode(', ', $selectList);

    return $oselect->hasModifier()
             ? "select {$oselect->getModifier()} $selectList"
             : "select $selectList";
  }

  protected function buildFrom($query)
  {
    $from = $this->osuql->getQuery($query)->getFrom();

    if (!$from)
      return '';

    return $this->osuql->hasQuery($from)
            ? " from @$from $from"
            : " from $from";
  }

  protected function buildJoin($query)
  {
    $join = $this->osuql->getQuery($query)->getJoin();

    if (empty($join))
      return '';

    $joinList = [];
    foreach ($join as $ojoin) {
      $table = $ojoin->getTable();
      $type = $ojoin->getType();
      $on = $ojoin->getOn();

      $table = $this->osuql->hasQuery($table)
                ? "@$table $table"
                : $table;

      $joinList[] = "$type join $table on $on";
    }

    $joinList = ' ' . implode(' ', $joinList);

    return $joinList;
  }

  protected function buildGroup($query)
  {
    $group = $this->osuql->getQuery($query)->getGroup();

    if (empty($group))
      return '';

    $group = implode(', ', $group);

    return " group by $group";
  }

  protected function buildWhere($query)
  {
    $whereList = $this->osuql->getQuery($query)->getWhere();
    $filterWhereList = $this->osuql->getQuery($query)->getFilterWhere();

    if (empty($whereList) && empty($filterWhereList))
      return '';

    $fieldList = $this->osuql->getQuery($query)->getFieldList();
    $fields = array_keys($fieldList);
    $aliases = array_values($fieldList);

    foreach ($whereList as &$where) {
      $where = str_replace($aliases, $fields, $where);
    }
    unset($where);

    foreach ($filterWhereList as $paramKey => &$filterWhere) {
      if (!$this->osuql->hasValuableParam($paramKey)) {
        unset($filterWhereList[$paramKey]);
        continue;
      }

      $filterWhere = str_replace($aliases, $fields, $filterWhere);
    }
    unset($filterWhere);

    $fullWhereList = array_merge($whereList, $filterWhereList);
    if (empty($fullWhereList))
      return '';

    $whereList = implode(' and ', $fullWhereList);

    return " where $whereList";
  }

  protected function buildHaving($query)
  {
    $having = $this->osuql->getQuery($query)->getHaving();

    if (empty($having))
      return '';

    $having = implode(' and ', $having);
    return " having $having";
  }

  protected function buildOrder($query)
  {
    $order = $this->osuql->getQuery($query)->getOrder();

    if (empty($order))
      return '';

    $orderList = [];
    foreach ($order as $oorder) {
      $field = $oorder->getField();
      $direction = $oorder->getDirection();
      $orderList[] = "$field $direction";
    }

    $orderList = implode(', ', $orderList);

    return " order by $orderList";
  }

  protected function buildLimit($query)
  {
    $bound = [];
    $oselect = $this->osuql->getQuery($query);

    if ($oselect->hasOffset()) $bound[] = $oselect->getOffset();
    if ($oselect->hasLimit()) $bound[] = $oselect->getLimit();

    if (empty($bound))
      return '';

    $bound = implode(', ', $bound);

    return " limit $bound";
  }
}
