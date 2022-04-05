<?php

namespace suql\manager;

/**
 * Действия выполняемые с сущностями
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Controller
{
    /**
     * @var array перечень сущностей на сохранение и удаление
     */
    private $persistList = [];
    /**
     * @var array перечень сущностей на удаление
     */
    private $deleteList = [];
    /**
     * Сохранение или обновление сущности
     * @param suql\syntax\SuQL $entity
     */
    public function persist($entity)
    {
        $this->persistList[] = $entity;
    }
    /**
     * Удаление сущности
     * @param suql\syntax\SuQL $entity
     */
    public function delete($entity)
    {
        $this->deleteList[] = $entity;
    }
    /**
     * Выполнение запрошенных действий над сущностями
     */
    public function run()
    {
        foreach ($this->persistList as $entity) {
            // TODO: Пока только сохранение
            return $this->saveEntity($entity);
        }

        foreach ($this->deleteList as $entity) {
            $this->deleteEntity($entity);
        }

        $this->persistList = [];
        $this->deleteList = [];
    }
    /**
     * Сохранение
     * @param suql\syntax\SuQL $entity
     */
    private function saveEntity($entity)
    {
        return $entity->route1();
    }
    /**
     * Удаление
     * @param suql\syntax\SuQL $entity
     */
    private function deleteEntity($entity)
    {
    }
}