<?php
namespace core;

class SuQLCommand extends SuQLQuery {
  protected $type = 'command';
  private $instruction;
  private $params;

  function __construct($osuql, $instruction, $params) {
    parent::__construct($osuql);
    $this->instruction = $instruction;
    $this->params = $params;
  }
}
