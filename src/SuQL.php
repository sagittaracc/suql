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
    if (!$this->suql) return null;

    // Processing the nested queries
    $nestedQueries = SuQLParser::getNestedQueries($this->suql);
    foreach ($nestedQueries as $name => $query) {
      parent::addQuery($name);

      if (!$this->SELECT($name, $query))
        return false;
    }

    // Processing the main query
    // $mainQuery = SuQLParser::getMainQuery($this->suql);
    // parent::addQuery('main');
    //
    // if (!$this->SELECT('main', $mainQuery))
    //   return false;

    return true;
  }

  public function SELECT($name, $query) {
    $clauses = SuQLParser::getSelectClauses($query);

    parent::addFrom($name, $clauses['table'][0]);
    
    if (isset($clauses['where'])) parent::addWhere($name, $clauses['where'][0]);
    if (isset($clauses['offset'])) parent::addOffset($name, $clauses['offset'][0]);
    if (isset($clauses['limit'])) parent::addLimit($name, $clauses['limit'][0]);

    if (isset($clauses['fields'])) {
      // Processing fields
    }

    if (isset($clauses['join'])) {
      // Processing join
    }

    return true;
  }
}
