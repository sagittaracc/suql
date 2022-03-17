<?php

namespace suql\db\pdo;

/**
 * Подключение
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class Connection
{
    /**
     * @var PDO $dbh интерфейс подключения к базе данных
     */
    protected $dbh;
    /**
     * @var array $config настройки подключения к базе данных
     */
    protected $config;
    /**
     * @var string $builder класс используемого билдера
     */
    protected $builder;
    /**
     * Конструктор
     * @param PDO $dbh интерфейс подключения к базе данных
     * @param array $config настройки подключения к базе данных
     */
    function __construct($dbh, $config)
    {
        $this->dbh = $dbh;
        $this->config = $config;
    }
    /**
     * Получить интерфейс подключения к базе данных
     * @return PDO
     */
    public function getPdo()
    {
        return $this->dbh;
    }
    /**
     * Получить используемый класс билдера
     * @return string
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
