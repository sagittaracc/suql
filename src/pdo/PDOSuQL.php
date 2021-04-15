<?php

abstract class PDOSuQL extends SuQL
{
  private $dbh;

  protected $driver = 'mysql';
  protected $host = 'localhost';
  protected $user = 'root';
  protected $password = '';
  protected $dbname = '';

  protected $data = [];

  function __construct($data = [])
  {
    if (!empty($data))
    {
      $this->data = $data;
    }

    parent::__construct(array_keys($data));

    $this->dbh = new PDO(
      "{$this->driver}:dbname={$this->dbname};host={$this->host}",
      $this->user,
      $this->password
    );
  }

  public function save()
  {
    $stmt = $this->dbh->prepare($this->getRawSql());

    foreach ($this->data as $field => $value)
    {
      $stmt->bindValue(":$field", $value, $this->getPDOParamType($value));
    }

    $stmt->execute();
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
    $this->params = array_merge($this->params, $params);

    $sql = $this->getRawSql();

    if (empty($this->params))
    {
      $stmt = $this->dbh->query($sql);
    }
    else
    {
      $stmt = $this->dbh->prepare($sql);

      foreach ($this->params as $param => $value)
      {
        if ($value)
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
