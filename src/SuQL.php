<?php
class SuQL extends SQLSugarSyntax
{
  const REGEX_NESTED_QUERY = '/@(?<name>[a-z0-9_]+)\s*=\s*(?<query>.*?);/msi';  // @<var_name> = <query>;

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

  public function interpret() {
    if (!$this->suql) return null;

    // Looking for the nested queries
    preg_match_all(self::REGEX_NESTED_QUERY, $this->suql, $nestedQueries);
    $nestedQueries = array_combine($nestedQueries['name'], $nestedQueries['query']);

    foreach ($nestedQueries as $name => $query) {
      parent::addQuery($name);

      if (!$this->SELECT($query))
        return false;
    }

    // Looking for the main query
    // if (!$this->SELECT('main')) return false;

    return true;
  }

  public function SELECT($query) {
    return true;
  }
}
