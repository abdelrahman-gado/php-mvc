<?php

use Framework\Router;

$router = new Router();

$router->add('/admin/users/index', [
    'namespace' => 'Admin',
    'controller' => 'UserController',
    'action' => 'index'
]);
$router->add('/{title}/{id:\d+}/{page:\d+}', ['controller' => 'ProductController', 'action' => 'showPage']);
$router->add('/products/create', ['controller' => 'ProductController', 'action' => 'create']);
$router->add('/products/new', ['controller' => 'ProductController', 'action' => 'new']);
$router->add('/products/{slug:[\w-]+}', ['controller' => 'ProductController', 'action' => 'show']);
$router->add('/{controller}/{id:\d+}/{action}');
$router->add('/home/index', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('/products', ['controller' => 'ProductController', 'action' => 'index']);
$router->add('/', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('/{controller}/{action}');

return $router;