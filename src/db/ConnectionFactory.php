<?php

namespace suql\db;

use suql\db\pdo\MySQLConnection;
use suql\db\pdo\PgSQLConnection;
use suql\db\pdo\SqliteConnection;

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
            case 'sqlite':
                return SqliteConnection::create($config);
            case 'pgsql':
                return PgSQLConnection::create($config);
        }
    }
}