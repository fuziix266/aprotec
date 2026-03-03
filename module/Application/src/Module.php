<?php

namespace Application;

use Laminas\Mvc\MvcEvent;

class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [],
        ];
    }

    public function getControllerConfig(): array
    {
        return [
            'factories' => [],
        ];
    }
}