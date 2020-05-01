<?php
class SuQL extends SQLSugarSyntax
{
  private $parser;

  function __construct() {
    $this->parser = new SuQLParser();
    parent::__construct();
  }

  public function clear() {
    $this->parser->clear();
    parent::clear();
  }

  public function getSQL() {
    if ($this->parse()) {
      $sql = parent::getSQL();
      $this->clear();
      return $sql;
    }

    return null;
  }

  public function getSQLObject() {
    if ($this->parse()) {
      $osuql = parent::getSQLObject();
      $this->clear();
      return $osuql;
    }

    return null;
  }

  public function query($suql) {
    $this->parser->setQuery($suql);
    return $this;
  }

  public function parse() {
    $nestedQueries = $this->parser->getNestedQueries();
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
