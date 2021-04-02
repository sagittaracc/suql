<?php

class PDOSuQL extends SuQL
{
  private $dbh;

  protected $driver = 'mysql';
  protected $host = 'localhost';
  protected $user = 'root';
  protected $password = '';
  protected $dbname = '';

  public static function db()
  {
    $instance = static::find();

    $instance->dbh = new PDO(
      "{$instance->driver}:dbname={$instance->dbname};host={$instance->host}",
      $instance->user,
      $instance->password
    );

    return $instance;
  }

  public function fetch()
  {
    $rows = [];

    foreach ($this->dbh->query($this->getRawSql()) as $row)
    {
      $rows[] = $row;
    }

    return $rows;
  }
}
