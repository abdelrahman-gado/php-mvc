<?php


declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];

    public function add(string $path, array $params = []): void
    {
        $this->routes[] = [
            'path' => $path,
            'params' => $params
        ];
    }

    public function match(string $path): array|bool
    {
        $path = trim($path, '/');
        foreach ($this->routes as $route) {
            $pattern = $this->getPatternFromPath($route['path']);
            if (preg_match($pattern, $path, $matches)) {
                $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $params = array_merge($matches, $route['params']);

                $params['controller'] = $this->mapParamToController($params['controller']);
                return $params;
            }
        }

        return false;
    }

    private function getPatternFromPath(string $routePath): string
    {
        $segments = explode('/', trim($routePath, '/'));
        $segments = array_map(function (string $segment) {
            if (preg_match("#^\{([a-z][a-z0-9]*)\}$#", $segment, $matches)) {
                return "(?<{$matches[1]}>[^/]*)";
            }
            if (preg_match("#^\{([a-z][a-z0-9]*):(.+)\}$#", $segment, $matches)) {
                return "(?<{$matches[1]}>{$matches[2]})";
            }
            return $segment;
        }, $segments);
        return '#^' . implode('/', $segments) . '$#iu';
    }

    private function mapParamToController(string $param): string
    {
        // Remove 's' from url controller parameter.
        $newParam = substr($param, 0, strlen($param) - 1);
        
        $controllersList = scandir(__DIR__ . '/../App/Controllers');
        $controllersList = array_filter(
            $controllersList,
            fn($controller) => str_contains($controller, '.php')
        );
        if (!$controllersList) {
            return $param;
        }
        
        foreach ($controllersList as $controller) {
            if (str_starts_with(strtolower($controller), strtolower($newParam))) {
                return str_replace('.php', '', $controller);
            }
        }

        return $param;
    }
}