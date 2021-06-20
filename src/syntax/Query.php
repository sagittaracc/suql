<?php

namespace suql\syntax;

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
     */
    public static function create($connection, $query = '')
    {
        $instance = new self();
        $instance->query = $query;
        $instance->db = Container::get($connection);

        return $instance;
    }
    /**
     * Задать запрос
     * @param string $query
     */
    public function query($query)
    {
        $this->query = $query;

        return $this;
    }
    /**
     * Выполнение запроса
     */
    public function exec()
    {
        return $this->db->exec($this->query);
    }
}
