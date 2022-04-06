<?php

namespace suql\manager;

use suql\annotation\RouteAnnotation;

/**
 * Действия выполняемые при работе с контроллерами
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class ControllerEntityManager
{
    /**
     * Запрос роута
     * @return mixed
     */
    public function fetch($entity)
    {
        $routes = $entity->getQuery($entity->query())->getSelect();
        $route = array_shift($routes);
        $annotation = RouteAnnotation::from($entity)->for('GET', $route->getName())->read();
        return $entity->{$annotation->function}();
    }
}