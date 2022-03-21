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
        if (is_string($repositoryQuery)) {
            if (class_exists($repositoryQuery)) {
                $repository = $repositoryQuery::all();
                $repository->setScheme($this->schemeClass);
                $repository->setBuilder($this->builderClass);
    
                return $repository;
            }
        }
        else if ($repositoryQuery instanceof Entity) {
        }

        return null;
    }
}