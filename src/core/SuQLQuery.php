<?php
namespace suql\core;

class SuQLQuery {
  protected $osuql = null;

  function __construct($osuql) {
    $this->osuql = $osuql;
  }

  public function getOSuQL() {
    return $this->osuql;
  }
}
