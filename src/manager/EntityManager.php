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
            $this->saveEntity($entity);
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
        $entity->init();
        $entity->addInsert($entity->query());
        $entity->getQuery($entity->query())->addInto($entity->table());
        foreach ($entity->getPublicProperties() as $property) {
            $entity->getQuery($entity->query())->addValue($property->getName(), $entity->{$property->getName()});
        }

        $db = $entity->getDb();

        $config = $db->getConfig();
        $table = $entity->table();

        $tableExistsQuery = $db->getPdo()->query($entity->getBuilder()->tableExistsQuery($config, $table));
        $tableExists = $tableExistsQuery && $table ? $tableExistsQuery->fetchColumn() : true;
        if (!$tableExists) {
            $entity->create();
            $db->getPdo()->query($entity->getBuilder()->buildModel($entity));
        }

        $entity->getDb()->getPdo()->exec($entity->getRawSql());
    }
    /**
     * Удаление
     * @param suql\syntax\SuQL $entity
     */
    private function deleteEntity($entity)
    {
    }
}