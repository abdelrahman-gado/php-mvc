<?php

declare(strict_types=1);

namespace Framework;

class PHPTemplateViewer implements TemplateViewerInterface
{
    private const VIEW_PATH = __DIR__ . '/../../views/';

    public function render(string $template, array $data = []): bool|string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require_once self::VIEW_PATH . $template;
        return ob_get_clean();
    }
}