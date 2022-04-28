<?php

namespace suql\syntax;

/**
 * Объект обращающийся к базе данных
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface DbObject
{
    /**
     * Ссылка на объект PDO
     * @return \suql\db\pdo\Connection
     */
    public function getDb();
}