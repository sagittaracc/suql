<?php

namespace suql\syntax\entity;

use Attribute;
use ReflectionClass;
use ReflectionMethod;
use suql\syntax\PhpInput;

#[Attribute]
class RPCMethod
{
    function __construct(
        public string $name
    ) {
    }
}

abstract class RPCRouter
{
    private array $methods = [];

    function __construct()
    {
        $class = new ReflectionClass($this);
        $methodList = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methodList as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();

                if (!($instance instanceof RPCMethod)) {
                    continue;
                }

                $parameters = $method->getParameters();
                
                $this->methods[$instance->name] = [$method, $parameters];
            }
        }
    }

    public function run()
    {
        $phpInput = new PhpInput();
        $body = json_decode($phpInput->get(), true);
        $a = $this->methods[$body['method']];
        $method = $a[0];
        $parameters = $a[1];

        $args = [];
        foreach ($parameters as $parameter) {
            $args[] = $body['params'][$parameter->name];
        }
        return call_user_func_array([new $method->class, $method->name], $args);
    }
}