<?php

namespace Framework;

use ReflectionClass;
use ReflectionMethod;

class Dispatcher
{
    public function __construct(private Router $router)
    {
    }

    public function handle(string $path): void
    {
        $params = $this->router->match($path);
        if ($params === false) {
            exit('No route matched');
        }

        $action = $params['action'];
        $namespace = '\\App\\Controllers';
        if (array_key_exists('namespace', $params)) {
            $namespace .= '\\' . $params['namespace'];
        }

        $controller = $namespace . '\\' . $params['controller'];
        // Auto Wiring Idea
        $controller_object = $this->getObject($controller);
        $controller_object->$action(...$this->getActionArguments($controller, $action, $params));
    }

    private function getObject(string $className): object
    {
        $dependencies = [];
        $reflector = new ReflectionClass($className);
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            return new $className;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $type = (string) $parameter->getType();
            $dependencies[] = $this->getObject($type);
        }

        return new $className(...$dependencies);
    }

    private function getActionArguments(string $controller, string $action, array $params): array
    {
        $reflectionMethod = new ReflectionMethod($controller, $action);
        return array_merge(
            ...array_map(
                fn($parameter) => [$parameter->getName() => $params[$parameter->getName()]],
                $reflectionMethod->getParameters()
            )
        );
    }

    private function getControllerName(array $params): string
    {
        $controller = $params['controller'];
        $controller = str_replace(".", "", ucwords(strtolower($controller), '-'));
        return "App\Controllers\\" . $controller;
    }

    private function getActionName(array $params): string
    {
        $action = $params['action'];
        $action = lcfirst(str_replace("-", "", ucwords(strtolower($action), "-")));
        return $action;
    }
}