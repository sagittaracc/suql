<?php
class SuQL extends SQLSugarSyntax
{
  private $suql;

  function __construct() {
    parent::__construct();
  }

  public function getSQL() {
    return $this->interpret() ? parent::getSQL() : null;
  }

  public function getSQLObject() {
    return $this->interpret() ? parent::getSQLObject() : null;
  }

  public function query($suql) {
    $this->suql = trim($suql);
    return $this;
  }

  public function interpret() {
    if (!$this->suql) return false;

    // Processing the nested queries
    $nestedQueries = SuQLParser::getNestedQueries($this->suql);
    foreach ($nestedQueries as $name => $query) {
      parent::addQuery($name);

      if (!$this->SELECT($name, $query))
        return false;
    }

    // Processing the main query
    $mainQuery = SuQLParser::getMainQuery($this->suql);
    parent::addQuery('main');

    if (!$this->SELECT('main', $mainQuery))
      return false;

    return true;
  }

  public function SELECT($name, $query) {
    $clauses = SuQLParser::getSelectClauses($query);

    if ($clauses['table'][0]  !== '') parent::addFrom($name, $clauses['table'][0]);
    if ($clauses['where'][0]  !== '') parent::addWhere($name, $clauses['where'][0]);
    if ($clauses['offset'][0] !== '') parent::addOffset($name, $clauses['offset'][0]);
    if ($clauses['limit'][0]  !== '') parent::addLimit($name, $clauses['limit'][0]);

    if ($clauses['join'][0] !== '') {
      $joinedTables = SuQLParser::getJoinedTables($clauses['join'][0]);
      foreach ($joinedTables as $join_type => $table)
        parent::addJoin($name, $join_type, $table);
    }

    if ($clauses['fields'][0] !== '') {
      $fieldList = SuQLParser::getFieldList($clauses['fields'][0]);
      for ($i = 0, $n = count($fieldList['name']); $i < $n; $i++) {
        $fieldName = parent::addField(
          $name,
          $clauses['table'][0],
          [$fieldList['name'][$i] => $fieldList['alias'][$i]]
        );
      }
    }

    return true;
  }
}
