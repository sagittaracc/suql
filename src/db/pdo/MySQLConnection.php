<?php

namespace suql\db\pdo;

use MySQLBuilder;
use PDO;

/**
 * MySQL
 *
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class MySQLConnection extends Connection
{
    protected $builder = MySQLBuilder::class;
    /**
     * @inheritdoc
     */
    public function __construct($config)
    {
        $dbh = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
            $config['user'],
            $config['pass']
        );

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        parent::__construct($dbh, $config);
    }
}
