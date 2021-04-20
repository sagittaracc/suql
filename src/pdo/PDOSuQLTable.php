<?php

abstract class PDOSuQLTable extends SuQLTable
{
  use PDOSuQL;

  public function save()
  {
    $stmt = $this->dbh->prepare($this->getRawSql());

    foreach ($this->data as $field => $value)
    {
      $stmt->bindValue(":$field", $value, $this->getPDOParamType($value));
    }

    $stmt->execute();
  }
}