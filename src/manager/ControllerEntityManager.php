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
     * Запрос роута
     * @return mixed
     */
    public function fetch($entity)
    {
        $routes = $entity->getQuery($entity->query())->getSelect();
        $route = array_shift($routes);
        // $route->getName()
        return $entity->route1();
    }
}