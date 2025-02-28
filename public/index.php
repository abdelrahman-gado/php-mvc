<?php

declare(strict_types=1);

use Framework\Dispatcher;
use Framework\Dotenv;
use Framework\Request;

spl_autoload_register(function ($className) {
    require_once __DIR__ . '/../src/' . ltrim(str_replace('\\', '/', $className), '\\') . '.php';
});

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

set_error_handler('Framework\ErrorHandler::handleError');
set_exception_handler('Framework\ErrorHandler::handleException');

$router = require_once __DIR__ . '/../config/route.php';

$container = require_once __DIR__ . '/../config/services.php';

// Dispatching
$dispatcher = new Dispatcher($router, $container);

$request = Request::createFromGlobals();
$response = $dispatcher->handle($request);
$response->send();
