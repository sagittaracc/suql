<?php
namespace core;

class SuQLQuery {
  protected $osuql = null;
  protected $type;

  function __construct($osuql) {
    $this->osuql = $osuql;
  }

  public function getOSuQL() {
    return $this->osuql;
  }

  public function getType() {
    return $this->type;
  }
}
