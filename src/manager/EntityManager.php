<?php

namespace suql\manager;

/**
 * Действия выполняемые с сущностями
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class EntityManager
{
    /**
     * @var array перечень сущностей на сохранение
     */
    private $saveList = [];
    /**
     * @var array перечень сущностей на обновление
     */
    private $updateList = [];
    /**
     * Сохранение сущности
     * @param suql\syntax\SuQL $entity
     */
    public function save($entity)
    {
        $this->saveList[] = $entity;
    }
    /**
     * Обновление сущности
     * @param suql\syntax\SuQL $entity
     */
    public function update($entity)
    {
        $this->updateList[] = $entity;
    }
    /**
     * Выполнение запрошенных действий над сущностями
     */
    public function run()
    {
        foreach ($this->saveList as $entity) {
        }

        foreach ($this->updateList as $entity) {
        }

        $this->saveList = [];
        $this->updateList = [];
    }
}