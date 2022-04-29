<?php

namespace suql\db;

use suql\db\pdo\Connection;
use suql\syntax\entity\SuQLTable;

/**
 * Для тех кому лень создавать классы для таблиц
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Entity extends SuQLTable
{
    /**
     * @var string имя сущности в базе данных
     */
    private $name;
    /**
     * @var string подключение к базе данных
     */
    private $connection = '';
    /**
     * Constructor
     * @param string $name имя таблицы в базе данных
     */
    function __construct(string $name)
    {
        $this->name = $name;
        parent::__construct();
    }
    /**
     * Получает имя сущности
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Устанавливает подключение
     * @param string $connection имя подключения
     * @return void
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
        parent::init();
        $this->setBuilder($this->getDb()->getBuilder());
    }
    /**
     * @inheritdoc
     */
    public function setScheme(string $schemeClass): void
    {
        parent::setScheme($schemeClass);

        $this->addSelect($this->query());
        $this->getSelect($this->query())->addFrom($this->name);
        $this->currentTable = $this->name;
    }
    /**
     * Наименование запроса
     * @return string
     */
    public function query(): string
    {
        return 'not_orm';
    }
    /**
     * Наименование таблицы
     * @return string
     */
    public function table(): string
    {
        return '';
    }
    /**
     * Перечень полей также не используется
     * @return array
     */
    public function fields(): array
    {
        return [];
    }
    /**
     * Возвращает подключение к базе данных
     * @return \suql\db\pdo\Connection
     */
    public function getDb(): Connection
    {
        return Container::get($this->connection);
    }
}
