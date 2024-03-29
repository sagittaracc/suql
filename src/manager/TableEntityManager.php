<?php

namespace suql\manager;

use suql\core\where\Equal;
use suql\syntax\ActiveRecord;

/**
 * Действия выполняемые с сущностями-таблицами
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class TableEntityManager
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
     * @param \suql\syntax\ActiveRecord $entity
     */
    public function persist($entity)
    {
        $this->persistList[] = $entity;
    }
    /**
     * Удаление сущности
     * @param \suql\syntax\ActiveRecord $entity
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
            if ($entity->isNewOne()) {
                $this->saveEntity($entity);
            }
            else {
                $this->updateEntity($entity);
            }
        }

        foreach ($this->deleteList as $entity) {
            $this->deleteEntity($entity);
        }

        $this->persistList = [];
        $this->deleteList = [];
    }
    /**
     * Сохранение
     * @param \suql\syntax\ActiveRecord $entity
     */
    private function saveEntity($entity)
    {
        $entity->init();
        $entity->addInsert($entity->query());
        $entity->getQuery($entity->query())->addInto($entity->table());
        $row = [];
        foreach ($entity->getPublicProperties() as $property) {
            $propertyName = $property->getName();
            if (is_subclass_of($entity->$propertyName, ActiveRecord::class)) {
                $subEntity = $entity->$propertyName;
                $this->saveEntity($subEntity);
                $entity->getQuery($entity->query())->addValue($propertyName, $subEntity->getLastInsertId());
            }
            else {
                $entity->getQuery($entity->query())->addValue($propertyName, $entity->$propertyName);
                $row[$propertyName] = $entity->$propertyName;
            }
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

        if ($entity->hasTrigger('insert')) {
            $entity->runTrigger('insert', $row);
        }

        $entity->setLastInsertId($entity->getDb()->getPdo()->lastInsertId());
    }
    /**
     * Обновление
     * @param \suql\syntax\ActiveRecord $entity
     */
    private function updateEntity($entity)
    {
        $entity->init();
        $entity->addUpdate($entity->query());
        $entity->getQuery($entity->query())->setTable($entity->table());

        foreach ($entity->getPublicProperties() as $property) {
            $propertyName = $property->getName();
            $entity->getQuery($entity->query())->addValue($propertyName, $entity->$propertyName);
        }

        $pk = $entity->getPrimaryKey();
        $entity->getQuery($entity->query())->addWhere($pk, Equal::integer($entity->$pk));
        $query = $entity->getRawSql();
        $entity->getDb()->getPdo()->exec($query);
    }
    /**
     * Удаление
     * @param \suql\syntax\ActiveRecord $entity
     */
    private function deleteEntity($entity)
    {
    }
}