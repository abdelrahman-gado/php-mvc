<?php

declare(strict_types=1);

use App\Database;
use Framework\Container;
use Framework\Dispatcher;
use Framework\Exceptions\PageNotFoundException;
use Framework\Router;

set_error_handler(function (
    int $errno,
    string $errstr,
    string $errfile,
    int $errline
) {
    throw new ErrorException($errstr, 500, $errno, $errfile, $errline);
});

set_exception_handler(function (Throwable $exception) {
    http_response_code($exception->getCode());
    $template = '500.php';
    if ($exception instanceof PageNotFoundException) {
        $template = '404.php';
    }

    $showErrors = true;
    if ($showErrors) {
        ini_set('display_errors', '1');
        throw $exception;
    } else {
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        require_once __DIR__ . "/views/{$template}";
    }
});

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === false) {
    throw new UnexpectedValueException("Malformed URL: '{$_SERVER['REQUEST_URI']}'", 500);
}

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
$container = new Container();
$container->set(Database::class, function () { 
    return new Database('localhost', 'product_db', 'product_db_user', 'secret');
});
$dispatcher = new Dispatcher($router, $container);
$dispatcher->handle($path);
