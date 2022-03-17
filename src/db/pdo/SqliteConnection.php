<?php

namespace suql\db\pdo;

use PDO;
use suql\builder\SqliteBuilder;

/**
 * Sqlite
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SqliteConnection extends Connection
{
    protected $builder = SqliteBuilder::class;
    /**
     * @inheritdoc
     */
    public function __construct($config)
    {
        $dbh = new PDO("sqlite:{$config['file']}");

        return parent::__construct($dbh, $config);
    }
}
