<?php

namespace suql\core;

/**
 * Управление именем поля. Расширает функциона простого имени посредством добавления к имени еще и имени таблицы
 * так как имя поля в базе данных определяется тремя параметрами: именем, таблицей и алиасом опционально
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class FieldName extends Name
{
    /**
     * @var string имя таблицы
     */
    public $table;
    /**
     * Constructor
     * @param string $table имя таблицы
     * @param string|array имя поля
     */
    function __construct($table, $name)
    {
        $this->table = $table;
        parent::__construct($name);
    }
}
