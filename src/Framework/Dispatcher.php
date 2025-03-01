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
        private array $middlewareClasses
    ) {
    }

    public function handle(Request $request): Response
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
        $controller_object->setResponse($this->container->get(Response::class));
        $controller_object->setViewer($this->container->get(TemplateViewerInterface::class));
        $controllerHandler = new ControllerRequestHandler($controller_object, $action, $this->getActionArguments($controller, $action, $params));
        $middleware = $this->getMiddleware($params);
        $middlewareHandler = new MiddlewareRequestHandler($middleware, $controllerHandler);
        return $middlewareHandler->handle($request);
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

    private function getMiddleware(array $params): array
    {
        if (!array_key_exists('middleware', $params)) {
            return [];
        }

        $middleware = explode("|", $params['middleware']);
        array_walk($middleware, function (&$value) {
            if (!array_key_exists($value, $this->middlewareClasses)) {
                throw new UnexpectedValueException("Middleware '{$value}' not found", 500);
            }
            $value = $this->container->get($this->middlewareClasses[$value]);
        });
        return $middleware;
    }
}