<?php

namespace suql\syntax;

use suql\builder\MySQLBuilder;
use suql\core\Scheme;
use suql\db\Container;

/**
 * Для тех кому лень создавать классы для таблиц
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class NotORM extends SuQL
{
    /**
     * @var PDO подключение к базе данных
     */
    private $connection;
    /**
     * Constructor
     * @param string|null $connection подключение к базе данных
     * @param string класс определяющий схему базы данных
     * @param string класс билдера
     */
    function __construct($connection = null, $schemeClass = Scheme::class, $builderClass = MySQLBuilder::class)
    {
        $this->connection = $connection;
        parent::__construct(new $schemeClass, new $builderClass);
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