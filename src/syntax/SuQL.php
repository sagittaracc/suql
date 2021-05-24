<?php

namespace suql\syntax;

use suql\builder\SQLDriver;
use suql\core\Obj;
use suql\core\Scheme;

/**
 * Синтаксический сахар для query builder
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQL extends Obj implements Model
{
    /**
     * Выборка всех данных из модели
     * @return self
     */
    public static function all()
    {
        $instance = new static(new Scheme(), new SQLDriver('mysql'));
        $instance->addSelect($instance->query());
        $instance->getQuery($instance->query())->addFrom($instance->table());

        return $instance;
    }
    /**
     * Выборка определенных полей модели
     * @return self
     */
    public function select($fieldList)
    {
        foreach ($fieldList as $field => $alias) {
            $this->getQuery($this->query())->addField($this->table(), [$field => $alias]);
        }

        return $this;
    }
    /**
     * Возвращает sql
     * @return string
     */
    public function getRawSql()
    {
        return $this->getSQL([$this->query()]);
    }
}