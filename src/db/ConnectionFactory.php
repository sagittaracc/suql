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
     * @return suql\db\pdo\Connection
     */
    public static function create($config)
    {
        switch ($config['driver']) {
            case 'mysql':
                return new MySQLConnection($config);
            case 'sqlite':
                return new SqliteConnection($config);
            case 'pgsql':
                return new PgSQLConnection($config);
        }
    }
}