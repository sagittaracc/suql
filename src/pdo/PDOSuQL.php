<?php

use suql\exception\DBFailConnectionException;

trait PDOSuQL
{
  private $dbh;

  protected $driver = 'mysql';
  protected $host = 'localhost';
  protected $user = 'root';
  protected $password = '';
  protected $dbname = '';

  public function createConnection()
  {
    try
    {
      $this->dbh = new PDO(
        "{$this->driver}:dbname={$this->dbname};host={$this->host}",
        $this->user,
        $this->password
      );
    }
    catch (PDOException $e)
    {
      throw new DBFailConnectionException();
    }
  }

  public function fetchAll()
  {
    $stmt = $this->fetch();

    $rows = [];
    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row)
    {
      $rows[] = $row;
    }

    return $rows;
  }

  public function fetchOne()
  {
    $stmt = $this->fetch();
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    return $row ? $row : null;
  }

  private function fetch()
  {
    $sql = $this->getRawSql();

    if (empty($this->params))
    {
      $stmt = $this->dbh->query($sql);
    }
    else
    {
      $stmt = $this->dbh->prepare($sql);

      foreach ($this->params as $suqlParam)
      {
        $paramList = $suqlParam->getParamList();
        foreach ($paramList as $placeholder => $value)
        {
          $stmt->bindValue($placeholder, $value, $this->getPDOParamType($value)); 
        }
      }

      $stmt->execute();
    }

    return $stmt;
  }

  private function getPDOParamType($param)
  {
    switch (gettype($param))
    {
      case 'boolean':
        return PDO::PARAM_BOOL;
      case 'integer':
        return PDO::PARAM_INT;
      case 'double':
        return PDO::PARAM_STR;
      case 'string':
        return PDO::PARAM_STR;
      case 'NULL':
        return PDO::PARAM_NULL;
      default:
        return PDO::PARAM_STR;
    }
  }
}
