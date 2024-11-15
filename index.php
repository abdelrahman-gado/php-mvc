<?php

use Framework\Dispatcher;
use Framework\Router;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
spl_autoload_register(function ($className) {
    require_once __DIR__ . '/src/' . ltrim(str_replace('\\', '/', $className), '\\') . '.php';
});

$router = new Router();

$router->add('/admin/users/index', [
    'namespace' => 'Admin',
    'controller' => 'UserController',
    'action' => 'index'
]);
$router->add('/{title}/{id:\d+}/{page:\d+}', ['controller' => 'ProductController', 'action' => 'showPage']);
$router->add('/products/{slug:[\w-]+}', ['controller' => 'ProductController', 'action' => 'show']);
$router->add('/{controller}/{id:\d+}/{action}');
$router->add('/home/index', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('/products', ['controller' => 'ProductController', 'action' => 'index']);
$router->add('/', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('/{controller}/{action}');

// Dispatching
$dispatcher = new Dispatcher($router);
$dispatcher->handle($path);
