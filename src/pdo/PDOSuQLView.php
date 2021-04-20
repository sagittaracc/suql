<?php

abstract class PDOSuQLView extends SuQLView
{
  use PDOSuQL;

  function __construct()
  {
    $this->createConnection();
  }
}