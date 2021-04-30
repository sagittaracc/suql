<?php

namespace suql\syntax;

use suql\core\SuQLObject;
use suql\core\SuQLScheme;
use suql\builder\SQLDriver;

/**
 * Сырые sql запросы и выражения
 * Запросы где не фигурируют таблицы
 * Хранимые процедуры и функции
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class RawSuQL extends SuQLObject implements SuQLQueryInterface
{
    /**
     * @var string по умолчанию используем MySQL
     */
    protected $dbms = 'mysql';
    /**
     * Constructor
     */
    function __construct()
    {
        $scheme = new SuQLScheme();
        $driver = new SQLDriver($this->dbms);
        parent::__construct($scheme, $driver);
    }
    /**
     * Название запроса
     * @return string
     */
    public function query()
    {
        return 'main';
    }
    /**
     * Название реализуемой таблицы
     * Сырые запросы не используют таблицы
     * @return null
     */
    public function table()
    {
        return null;
    }
    /**
     * Сконвертировать в SQL
     * @return string
     */
    public function getRawSql()
    {
        return parent::getSQL([$this->query()]);
    }
    /**
     * Select запросы вида 'select 1, 2, 3'
     * @return self
     */
    public static function select()
    {
        $instance = new static();
        $instance->addSelect($instance->query());
        return $instance;
    }
    /**
     * Хранимая процедура
     * @return self
     */
    public static function proc($name)
    {
        $instance = new static();
        $instance->addProcedure($instance->query(), $name);
        return $instance;
    }
    /**
     * Хранимая функция
     * @return self
     */
    public static function func($name)
    {
        $instance = new static();
        $instance->addFunction($instance->query(), $name);
        return $instance;
    }
    /**
     * Добавить поле в сырой запрос select
     * @return self
     */
    public function field($raw)
    {
        $this->getQuery($this->query())->addField($this->table(), $raw);
        return $this;
    }
}
