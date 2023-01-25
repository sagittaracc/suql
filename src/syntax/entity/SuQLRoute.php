<?php

namespace suql\syntax\entity;

use Attribute;
use Exception;
use ReflectionClass;
use ReflectionMethod;

#[Attribute]
class Route
{
    function __construct(
        public string $endpoint,
        public string $verb = 'GET'
    ) {
    }
}

abstract class SuQLRoute
{
    private array $routes = [];

    function __construct()
    {
        $routes = [];
        $controller = new ReflectionClass($this);
        $actions = $controller->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($actions as $action) {
            $attributes = $action->getAttributes();

            foreach ($attributes as $attribute) {
                $route = $attribute->newInstance();

                if (!($route instanceof Route)) {
                    continue;
                }

                $verb = $route->verb;
                $endpoint = $route->endpoint;

                $routes[$verb][$endpoint] = $action;
            }
        }

        $this->routes = $routes;
    }

    public function run($endpoint)
    {
        foreach ($this->routes['GET'] as $route => $callback) {
            if (preg_match("`$route`", $endpoint, $matches)) {
                array_shift($matches);
                return call_user_func_array(array(new $callback->class, $callback->name), $matches);
            }
        }

        throw new Exception('404');
    }
}
