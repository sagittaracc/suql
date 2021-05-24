<?php

namespace suql\syntax;

use suql\builder\SQLDriver;
use suql\core\Obj;
use suql\core\Scheme;
use sagittaracc\ArrayHelper;

/**
 * SuQL синтаксис
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQL extends Obj implements QueryObject
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
        if (ArrayHelper::isSequential($fieldList)) {
            foreach ($fieldList as $field) {
                $this->getQuery($this->query())->addField($this->table(), $field);
            }
        }
        else {
            foreach ($fieldList as $field => $alias) {
                $this->getQuery($this->query())->addField($this->table(), [$field => $alias]);
            }
        }

        return $this;
    }
    /**
     * Сортировка
     * @return self
     */
    public function order($order)
    {
        foreach ($order as $field => $direction) {
            $this->getQuery($this->query())->addField($this->table(), $field, false);
            $this->getQuery($this->query())->getField($this->table(), $field)->addModifier($direction);
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