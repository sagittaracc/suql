<?php

namespace suql\manager;

use suql\db\Entity;

/**
 * Действия выполняемые с сущностями
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class EntityManager
{
    /**
     * @var string используемая схема
     */
    private $schemeClass;
    /**
     * @var string используемый билдер
     */
    private $builderClass;
    /**
     * Задать схему
     * @param string $schemeClass
     */
    public function setScheme($schemeClass)
    {
        $this->schemeClass = $schemeClass;
    }
    /**
     * Задать билдер
     * @param string $builderClass
     */
    public function setBuilder($builderClass)
    {
        $this->builderClass = $builderClass;
    }
    /**
     * Получить объект из репозитория
     * @param string имя класса объекта или таблицы в базе данных
     * @return suql\syntax\SuQL
     */
    public function getRepository($repositoryQuery)
    {
        $repository = null;

        if (is_string($repositoryQuery)) {
            if (class_exists($repositoryQuery)) {
                $repository = $repositoryQuery::all();

                $repository->setScheme($this->schemeClass);
                $repository->setBuilder($this->builderClass);
            }
            else {
                $repository = new Entity($repositoryQuery);

                $repository->setScheme($this->schemeClass);
                $repository->setBuilder($this->builderClass);
    
                $repository->addSelect($repository->query());
                $repository->getQuery($repository->query())->addFrom($repository->getName());
                $repository->setCurrentTable($repository->getName());
            }
        }

        return $repository;
    }
}