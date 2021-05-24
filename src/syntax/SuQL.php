<?php

namespace suql\syntax;

use app\schema\AppScheme;
use suql\builder\SQLDriver;
use suql\core\Obj;
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
        // TODO: AppScheme должна прописываться как то через конфиг
        $instance = new static(new AppScheme(), new SQLDriver('mysql'));
        $instance->addSelect($instance->query());
        $instance->getQuery($instance->query())->addFrom($instance->table());

        return $instance->view();
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
     * Сцепление таблиц
     * @return self
     */
    public function join($table)
    {
        $this->getQuery($this->query())->addJoin('inner', $table);

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