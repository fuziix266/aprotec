<?php

namespace Application;

return [
    'router' => [
        'routes' => [
            'application' => [
                'type'    => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'application' => __DIR__ . '/../view',
        ],
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'doctype' => 'HTML5',
    ],
];
