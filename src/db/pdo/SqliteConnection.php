<?php

namespace suql\db\pdo;

use PDO;

/**
 * Sqlite
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SqliteConnection extends Connection
{
    /**
     * @inheritdoc
     */
    public static function create($config)
    {
        return new PDO("sqlite:{$config['file']}");
    }
}
