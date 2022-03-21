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
     * @var string имя сущности в базе данных
     */
    private $name;
    /**
     * @var PDO подключение к базе данных
     */
    private $connection;
    /**
     * Constructor
     */
    function __construct($name)
    {
        $this->name = $name;
        parent::__construct();
    }
    /**
     * Получает имя сущности
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @inheritdoc
     */
    public function setScheme($schemeClass)
    {
        parent::setScheme($schemeClass);

        $this->addSelect($this->query());
        $this->getQuery($this->query())->addFrom($this->name);
        $this->currentTable = $this->name;
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
}
