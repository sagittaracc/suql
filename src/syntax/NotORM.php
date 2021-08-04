<?php

namespace suql\syntax;

use suql\builder\MySQLBuilder;
use suql\core\Scheme;

/**
 * Для тех кому лень создавать классы для таблиц
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class NotORM extends SuQL
{
    private $connection;

    function __construct($connection = null)
    {
        $this->connection = $connection;
        parent::__construct(new Scheme, new MySQLBuilder);
    }

    public function query()
    {
        return 'not_orm';
    }

    public function table()
    {
        return null;
    }

    public function create()
    {
        return [];
    }

    public function fields()
    {
        return [];
    }

    public function getDb()
    {
        return $this->connection;
    }

    public function entity($name)
    {
        $this->addSelect($this->query());
        $this->getQuery($this->query())->addFrom($name);
        $this->currentTable = $name;

        return $this;
    }
}