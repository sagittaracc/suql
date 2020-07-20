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

  public function setSuQL($suql) {
    $this->suql = $suql;
  }

  public function addUnionType($unionType, $table) {
    $this->suql .= " $unionType $table";
  }
}
