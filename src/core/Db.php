<?php

namespace suql\core;

use PDO;

/**
 * Обработчик контейнера подключений к базе данных
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Db
{
    /**
     * @var array контейнер подключений
     */
    private static $container;
    /**
     * Получает подключение по имени из контейнера
     * @param string $name
     * @return PDO
     */
    public static function get($name)
    {
        if (!isset(self::$container[$name])) {
            $method = "set$name";
            self::$container[$name] = static::$method();
        }

        return self::$container[$name];
    }
}