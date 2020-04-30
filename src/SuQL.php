<?php
class SuQL extends SQLSugarSyntax
{
  private $suql;

  function __construct() {
    parent::__construct();
    $this->suql = '';
  }

  public function query($suql) {
    $this->suql = trim($suql);
    return $this;
  }

  public function getSQL() {
    return $this->interpret() ? parent::getSQL() : null;
  }

  public function getSQLObject() {
    return $this->interpret() ? parent::getSQLObject() : null;
  }

  private function interpret() {
    if (!$this->suql) return null;

    $regex_select = 'select\s*'.
                    'from\s*@?(?<table>[a-z]+)'.      // the table to get the data from
                    '.*?'.                            // the field list
                    '(where\s*(?<where>.*?))?\s*'.    // where clause
                    '(offset\s*(?<offset>\d+))?\s*'.  // offset clause
                    '(limit\s*(?<limit>\d+))?;';      // limit clause

    // Looking for the nested queries
    preg_match_all("/@(?<name>[a-z]+)\s*=\s*$regex_select/msi", $this->suql, $statements);
    for ($i = 0, $n = count($statements['name']); $i < $n; $i++) {
      $query = $statements['name'][$i];
      parent::addQuery ( $query );
      parent::addFrom  ( $query, $statements['table'][$i]  );
      parent::addWhere ( $query, $statements['where'][$i]  );
      parent::addOffset( $query, $statements['offset'][$i] );
      parent::addLimit ( $query, $statements['limit'][$i]  );
    }

    // Looking for the main query
    preg_match_all("/^;?\s*$regex_select$/msi", $this->suql, $statements);
    if ($statements['table']) {
      parent::addQuery ( 'main' );
      parent::addFrom  ( 'main', $statements['table'][0]  );
      parent::addWhere ( 'main', $statements['where'][0]  );
      parent::addOffset( 'main', $statements['offset'][0] );
      parent::addLimit ( 'main', $statements['limit'][0]  );
    }

    return true;
  }
}
