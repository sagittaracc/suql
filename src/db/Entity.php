<?php

namespace suql\db;

use suql\syntax\SuQL;

/**
 * Для тех кому лень создавать классы для таблиц
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Entity extends SuQL
{
    /**
     * @var PDO подключение к базе данных
     */
    private $connection;
    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Устанавливает подключение
     * @param string $connection имя подключения
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }
    /**
     * Наименование запроса
     * @return string
     */
    public function query()
    {
        return 'not_orm';
    }
    /**
     * Наименование таблицы
     * Должно возвращать null
     * @return null
     */
    public function table()
    {
        return null;
    }
    /**
     * Так как нет таблицы то нет и описания для её создания
     * @return []
     */
    public function create()
    {
        return [];
    }
    /**
     * Перечень полей также не используется
     * @return []
     */
    public function fields()
    {
        return [];
    }
    /**
     * Возвращает подключение к базе данных
     * @return PDO
     */
    public function getDb()
    {
        return Container::get($this->connection);
    }
    /**
     * Загружает сущность для которой вы поленились описывать модель
     * @return self
     */
    public function entity($name)
    {
        $this->addSelect($this->query());
        $this->getQuery($this->query())->addFrom($name);
        $this->currentTable = $name;

        return $this;
    }
}
