<?php

namespace suql\db;

use suql\db\pdo\MySQLConnection;

/**
 * Фабрика подключений
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class ConnectionFactory
{
    /**
     * Создает подключение
     * @param array $config настройки подключения
     * @return mixed
     */
    public static function create($config)
    {
        switch ($config['driver']) {
            case 'mysql':
                return MySQLConnection::create($config);
        }
    }
}