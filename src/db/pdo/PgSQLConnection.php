<?php

namespace suql\db\pdo;

use PDO;
use suql\builder\PgSQLBuilder;

/**
 * PgSQL
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class PgSQLConnection extends Connection
{
    protected $builder = PgSQLBuilder::class;
    /**
     * @inheritdoc
     */
    public function __construct($config)
    {
        $dbh = new PDO(
            "pgsql:host={$config['host']};dbname={$config['dbname']}",
            $config['user'],
            $config['pass']
        );

        return parent::__construct($dbh, $config);
    }
}
