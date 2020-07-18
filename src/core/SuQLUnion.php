<?php
namespace core;

class SuQLUnion extends SuQLQuery {
  protected $type = 'union';
  private $suql   = '';

  function __construct($osuql, $suql) {
    parent::__construct($osuql);
    $this->suql = $suql;
  }

  public function getSuql() {
    return $this->suql;
  }
}
