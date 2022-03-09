<?php

namespace suql\db\pdo;

/**
 * Подключение
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class Connection
{
    /**
     * Создает подключение
     * @param array $config настройки подключения
     * @return PDO
     */
    abstract public static function create($config);
}
