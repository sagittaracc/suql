<?php
namespace core;

abstract class SuQLQuery {
  protected $osuql = null;
  protected $type;

  function __construct($osuql) {
    $this->osuql = $osuql;
  }

  abstract public function getSemantic();

  public function getOSuQL() {
    return $this->osuql;
  }

  public function getType() {
    return $this->type;
  }
}
