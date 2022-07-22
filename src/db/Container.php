<?php

namespace suql\db;

/**
 * Обработчик контейнера подключений к базе данных
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Container
{
    /**
     * @var array контейнер подключений
     */
    private static $items;
    /**
     * @var array контейнер триггеров
     */
    private static $triggers;
    /**
     * Создает контейнер с подключениями
     * @param array $dbList
     */
    public static function create($dbList)
    {
        self::add($dbList);
    }
    /** Добавление подключения
     * @param array $dbList
     */
    public static function add($dbList)
    {
        foreach ($dbList as $connection => $config) {
            self::$items[$connection] = ConnectionFactory::create($config);
        }
    }
    /**
     * Получает подключение из контейнера
     * @return \suql\db\pdo\Connection
     */
    public static function get($connection)
    {
        return isset(self::$items[$connection]) ? self::$items[$connection] : null;
    }
    /**
     * Добавляет триггер
     * @param string $modelClass
     * @param string $type
     * @param \Closure $callback
     */
    public static function addTrigger($modelClass, $type, $callback)
    {
        self::$triggers[$modelClass][$type] = $callback;
    }
    /**
     * Получает триггер
     * @param string $modelClass
     * @param string $type
     * @return \Closure|null
     */
    public static function getTrigger($modelClass, $type)
    {
        return isset(self::$triggers[$modelClass][$type]) ? self::$triggers[$modelClass][$type] : null;
    }
}
