<?php
namespace core;

class SuQLUnion extends SuQLQuery {
  protected $type = 'union';
  private $suql   = '';

  function __construct($osuql, $suql) {
    parent::__construct($osuql);
    $this->suql = $suql;
  }

  public function getSemantic() {
    return 'sql';
  }

  public function getSuql() {
    return $this->suql;
  }

  public function setSuQL($suql) {
    $this->suql = $suql;
  }

  public function addUnionTable($unionType, $table) {
    $this->suql .= " $unionType $table";
  }
}
