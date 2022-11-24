<?php

namespace suql\syntax;

use sagittaracc\PlaceholderHelper;
use suql\db\Container;

/**
 * Выполнение сырого SQL
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Query implements DbObject
{
    /**
     * @var \suql\db\pdo\Connection
     */
    private $db;
    /**
     * @var string запрос
     */
    private $query;
    /**
     * Создание нового запроса
     * @param string $query
     * @return self
     */
    public static function create($query = '')
    {
        $instance = new self();
        $instance->db = null;
        $instance->query = $query;

        return $instance;
    }
    /**
     * Задать подключение
     * @param \suql\db\pdo\Connection $db
     */
    public function setDb($db)
    {
        $this->db = $db;
        return $this;
    }
    /**
     * Реализация getDb
     * @return \suql\db\pdo\Connection
     */
    public function getDb()
    {
        return $this->db;
    }
    /**
     * Установить подключение к бд
     * @param string $connection
     */
    public function setConnection($connection)
    {
        return $this->setDb(Container::get($connection));
    }
    /**
     * Внедрить запрос в сырой sql
     * @param array $queries
     * @return self
     */
    public function bind($queries)
    {
        $suqlStringPlaceholder = (new PlaceholderHelper())->setQuote('');

        foreach ($queries as $query) {
            $this->query = $suqlStringPlaceholder->setString($this->query)->bind("({$query->getRawSql()})");
        }

        return $this;
    }
    /**
     * Задать запрос
     * @param string $query
     * @return self
     */
    public function query($query)
    {
        $this->query = $query;
        return $this;
    }
    /**
     * Вернуть текущий query
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
    /**
     * Выполнение запроса
     * @param array $params
     * @return mixed|array
     */
    public function exec($params = [])
    {
        // TODO: Добавить проверку установлено ли соединение
        // if (!$this->db) {
        //     throw new ...
        // }
        return empty($params)
            ? $this->db->getPdo()->exec($this->query)
            : $this->db->getPdo()->prepare($this->query)->execute($params);
    }
}
