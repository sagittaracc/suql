<?php

namespace suql\syntax;

use sagittaracc\PlaceholderHelper;
use suql\db\Container;

/**
 * Execute Raw sql
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Query
{
    /**
     * Db connection
     * @var PDO
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
    public static function create($connection, $query = '')
    {
        $instance = new self();
        $instance->query = $query;
        $instance->db = Container::get($connection);

        return $instance;
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
        return empty($params)
            ? $this->db->exec($this->query)
            : $this->db->prepare($this->query)->execute($params);
    }
}
