<?php

abstract class PDOSuQLView extends SuQLView
{
  use PDOSuQL;

  function __construct()
  {
    parent::__construct();
    $this->createConnection();
  }
}
