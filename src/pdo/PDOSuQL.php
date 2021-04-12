<?php

abstract class PDOSuQL extends SuQL
{
  private $dbh;

  protected $driver = 'mysql';
  protected $host = 'localhost';
  protected $user = 'root';
  protected $password = '';
  protected $dbname = '';

  public static function find()
  {
    $instance = parent::find();

    $instance->dbh = new PDO(
      "{$instance->driver}:dbname={$instance->dbname};host={$instance->host}",
      $instance->user,
      $instance->password
    );

    return $instance;
  }

  public function fetchAll($params = [])
  {
    $stmt = $this->fetch($params);

    $rows = [];
    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row)
    {
      $rows[] = $row;
    }

    return $rows;
  }

  public function fetchOne($params = [])
  {
    $stmt = $this->fetch($params);
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    return $row ? $row : null;
  }

  private function fetch($params = [])
  {
    if (empty($params))
    {
      $stmt = $this->dbh->query($this->getRawSql());
    }
    else
    {
      $stmt = $this->dbh->prepare($this->getRawSql());

      foreach ($params as $param => $value)
      {
        $stmt->bindValue($param, $value, $this->getPDOParamType($value));
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
