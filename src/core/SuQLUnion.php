<?php
namespace suql\core;

use suql\core\interface\UnionQueryInterface;

class SuQLUnion extends SuQLQuery implements UnionQueryInterface
{
  private $suql   = '';

  function __construct($osuql, $suql) {
    parent::__construct($osuql);
    $this->suql = $suql;
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
