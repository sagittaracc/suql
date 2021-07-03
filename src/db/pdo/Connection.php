<?php

namespace suql\db\pdo;

use PDO;

/**
 * PDO подключение
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Connection
{
    /**
     * Создает PDO подключение
     * @param array $config настройки подключения
     * @return PDO
     */
    public static function create($config)
    {
        return new PDO(
            "{$config['driver']}:host={$config['host']};dbname={$config['dbname']};charset=utf8",
            $config['user'],
            $config['pass']
        );
    }
}
