<?php

namespace suql\db\pdo;

use PDO;

/**
 * MySQL
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class MySQLConnection extends Connection
{
    /**
     * @inheritdoc
     */
    public static function create($config)
    {
        return new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
            $config['user'],
            $config['pass']
        );
    }
}
