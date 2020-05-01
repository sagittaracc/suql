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

    $nestedQueries = SuQLParser::getNestedQueries($this->suql);
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
