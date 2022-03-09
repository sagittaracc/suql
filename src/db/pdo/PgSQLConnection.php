<?php

namespace suql\db\pdo;

use PDO;

/**
 * PgSQL
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class PgSQLConnection extends Connection
{
    /**
     * @inheritdoc
     */
    public static function create($config)
    {
        return new PDO(
            "pgsql:host={$config['host']};dbname={$config['dbname']}",
            $config['user'],
            $config['pass']
        );
    }
}
