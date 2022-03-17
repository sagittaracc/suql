<?php

namespace suql\db\pdo;

/**
 * Подключение
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class Connection
{
    protected $dbh;
    protected $config;
    protected $builder;
    function __construct($dbh, $config)
    {
        $this->dbh = $dbh;
        $this->config = $config;
    }

    public function getPdo()
    {
        return $this->dbh;
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
