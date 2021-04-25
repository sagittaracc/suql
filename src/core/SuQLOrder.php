<?php
namespace suql\core;

class SuQLOrder {
  private $field;
  private $direction;

  function __construct($field, $direction) {
    $this->field = $field;
    $this->direction = $direction;
  }

  public function getField() {
    return $this->field;
  }

  public function getDirection() {
    return $this->direction;
  }
}
