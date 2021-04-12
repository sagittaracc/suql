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

  public function fetchAll()
  {
    $rows = [];

    $stmt = $this->dbh->query($this->getRawSql());

    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row)
    {
      $rows[] = $row;
    }

    return $rows;
  }

  public function fetchOne()
  {
    $stmt = $this->dbh->query($this->getRawSql());

    $row = $stmt->fetch(PDO::FETCH_OBJ);

    return $row ? $row : null;
  }
}
