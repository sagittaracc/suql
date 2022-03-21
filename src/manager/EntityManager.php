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
    private $schemeClass;

    private $builderClass;

    public function setScheme($schemeClass)
    {
        $this->schemeClass = $schemeClass;
    }

    public function setBuilder($builderClass)
    {
        $this->builderClass = $builderClass;
    }

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