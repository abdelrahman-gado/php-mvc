<?php

declare(strict_types=1);

namespace Framework;

use ErrorException;
use Framework\Exceptions\PageNotFoundException;
use Throwable;

class ErrorHandler
{
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ) {
        throw new ErrorException($errstr, 500, $errno, $errfile, $errline);
    }

    public static function handleException(Throwable $exception): void
    {
        http_response_code($exception->getCode());
        $template = '500.php';
        if ($exception instanceof PageNotFoundException) {
            $template = '404.php';
        }

        if ($_ENV['SHOW_ERRORS']) {
            ini_set('display_errors', '1');
            throw $exception;
        } else {
            ini_set('display_errors', '1');
            ini_set('log_errors', '0');
            require_once __DIR__ . "/../../views/{$template}";
        }
    }
}