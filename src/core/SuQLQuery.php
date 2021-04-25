<?php
namespace suql\core;

abstract class SuQLQuery {
  protected $osuql = null;

  function __construct($osuql) {
    $this->osuql = $osuql;
  }

  abstract public function getType();
  abstract public function getSemantic();

  public function getOSuQL() {
    return $this->osuql;
  }
}
