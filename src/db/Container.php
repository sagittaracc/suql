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
     * @return suql\db\pdo\Connection|null
     */
    public static function get($connection)
    {
        return isset(self::$items[$connection]) ? self::$items[$connection] : null;
    }
}
