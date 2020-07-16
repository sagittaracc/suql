<?php
class SuQLObject {
  private $queries;
  private $scheme;
  private $adapter;

  function __construct() {
    $this->queries = [];
    $this->scheme = ['rel' => [], 'temp_rel' => []];
    $this->adapter = null;
  }

  public function clear() {
    $this->queries = [];
    $this->scheme['temp_rel'] = [];
  }

  public function drop() {
    $this->queries = [];
    $this->scheme['rel'] = [];
    $this->scheme['temp_rel'] = [];
    $this->adapter = null;
  }

  public function setAdapter($adapter) {
    if (SQLAdapter::exists($adapter))
      $this->adapter = $adapter;

    return $this;
  }

  public function getSQLObject() {
    $osuql = [];

    foreach ($this->queries as $name => $query) {
      $osuql[$name] = $query->getSQLObject();
    }

    $this->clear();

    return $osuql;
  }
}
