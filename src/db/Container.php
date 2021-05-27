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
        foreach ($dbList as $connection => $config) {
            self::$items[$connection] = pdo\Connection::create($config);
        }
    }
    /**
     * Получает подключение из контейнера
     * @return PDO|null
     */
    public static function get($connection)
    {
        return isset(self::$items[$connection]) ? self::$items[$connection] : null;
    }
}
