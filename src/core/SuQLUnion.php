<?php
namespace suql\core;

class SuQLUnion extends SuQLQuery {
  private $suql   = '';

  function __construct($osuql, $suql) {
    parent::__construct($osuql);
    $this->suql = $suql;
  }

  public function getType() {
    return 'union';
  }

  public function getSemantic() {
    return 'sql';
  }

  public function getSuQL() {
    return $this->suql;
  }

  public function setSuQL($suql) {
    $this->suql = $suql;
  }

  public function addUnionTable($unionType, $table) {
    $this->suql .= " $unionType $table";
  }
}
