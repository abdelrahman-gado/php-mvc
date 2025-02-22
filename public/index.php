<?php

declare(strict_types=1);

use Framework\Dispatcher;
use Framework\Dotenv;

spl_autoload_register(function ($className) {
    require_once __DIR__ . '/../src/' . ltrim(str_replace('\\', '/', $className), '\\') . '.php';
});

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

set_error_handler('Framework\ErrorHandler::handleError');
set_exception_handler('Framework\ErrorHandler::handleException');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === false) {
    throw new UnexpectedValueException("Malformed URL: '{$_SERVER['REQUEST_URI']}'", 500);
}

$router = require_once __DIR__ . '/../config/route.php';

$container = require_once __DIR__ . '/../config/services.php';

// Dispatching
$dispatcher = new Dispatcher($router, $container);
$dispatcher->handle($path, $_SERVER['REQUEST_METHOD']);
