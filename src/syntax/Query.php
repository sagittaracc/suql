<?php

namespace suql\syntax;

use sagittaracc\PlaceholderHelper;
use suql\db\Container;
use suql\syntax\exception\ConnectionIsNotSet;

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
     * @return \suql\db\pdo\Connection
     */
    public function getDb()
    {
        return $this->db;
    }
    /**
     * Задать подключение по имени
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
        $placeholder = (new PlaceholderHelper())->setQuote('');

        foreach ($queries as $queryName => $query) {
            $rawQuery = $query->getRawSql();
            $placeholder->setString($this->query);

            $this->query = is_string($queryName)
                ? $placeholder->bind([$queryName => '('.$rawQuery.')'])
                : $placeholder->bind('('.$rawQuery.')');
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
        if (!$this->db) {
            throw new ConnectionIsNotSet;
        }

        return empty($params)
            ? $this->db->getPdo()->exec($this->query)
            : $this->db->getPdo()->prepare($this->query)->execute($params);
    }
}
