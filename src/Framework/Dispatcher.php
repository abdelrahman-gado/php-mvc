<?php

declare(strict_types=1);

namespace Framework;

use Framework\Exceptions\PageNotFoundException;
use ReflectionMethod;
use UnexpectedValueException;

class Dispatcher
{
    public function __construct(
        private Router $router,
        private Container $container,
    ) {
    }

    public function handle(Request $request): void
    {
        $path = $this->getPath($request->uri);
        $params = $this->router->match($path, $request->method);
        if ($params === false) {
            throw new PageNotFoundException("No route matched for '{$path}' with method '{$request->method}'");
        }

        $action = $params['action'];
        $namespace = '\\App\\Controllers';
        if (array_key_exists('namespace', $params)) {
            $namespace .= '\\' . $params['namespace'];
        }

        $controller = $namespace . '\\' . $params['controller'];
        // Auto Wiring Idea
        $controller_object = $this->container->get($controller);
        $controller_object->setRequest($request);
        $controller_object->setViewer($this->container->get(TemplateViewerInterface::class));
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

    private function getPath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if ($path === false) {
            throw new UnexpectedValueException("Malformed URL: '{$uri}'", 500);
        }

        return $path;
    }
}