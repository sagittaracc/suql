<?php

namespace suql\manager;

/**
 * Действия выполняемые при работе с контроллерами
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class ControllerEntityManager
{
    /**
     * @var array перечень сущностей на сохранение и удаление
     */
    private $persistList = [];
    /**
     * Сохранение или обновление сущности
     * @param suql\syntax\SuQL $entity
     */
    public function persist($entity)
    {
        $this->persistList[] = $entity;
    }
    /**
     * Выполнение запрошенных действий над сущностями
     */
    public function run()
    {
        $entity = end($this->persistList);
        $this->persistList = [];

        return $entity->route1();
    }
}