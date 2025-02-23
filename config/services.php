<?php

use App\Database;
use Framework\Container;
use Framework\MVCTemplateViewer;
use Framework\TemplateViewerInterface;

$container = new Container();
$container->set(Database::class, function () {
    return new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
});

$container->set(TemplateViewerInterface::class, function () {
    return new MVCTemplateViewer();
});

return $container;