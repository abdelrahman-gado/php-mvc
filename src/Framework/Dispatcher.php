<?php

declare(strict_types=1);

namespace Framework;

use Framework\Exceptions\PageNotFoundException;
use ReflectionMethod;

class Dispatcher
{
    public function __construct(
        private Router $router,
        private Container $container,
    ) {
    }

    public function handle(string $path, string $method): void
    {
        $params = $this->router->match($path, $method);
        if ($params === false) {
            throw new PageNotFoundException("No route matched for '$path' with method '$method'");
        }

        $action = $params['action'];
        $namespace = '\\App\\Controllers';
        if (array_key_exists('namespace', $params)) {
            $namespace .= '\\' . $params['namespace'];
        }

        $controller = $namespace . '\\' . $params['controller'];
        // Auto Wiring Idea
        $controller_object = $this->container->get($controller);
        $controller_object->$action(...$this->getActionArguments($controller, $action, $params));
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