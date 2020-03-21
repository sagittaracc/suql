<?php
class SQLBuilder
{
  private $SQLObject = null;
  private $sql = null;

  function __construct($SQLObject)
  {
    $this->SQLObject = $SQLObject;
  }

  public function getSql()
  {
    return $this->sql;
  }

  public function run()
  {
    if ($this->SQLObject)
    {
      // Building sql...
      $this->sql = $this->SQLObject;
    }
  }
}
