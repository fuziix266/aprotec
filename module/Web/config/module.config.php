<?php

namespace Web;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'web' => [
                'type'    => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/:action',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
            'web' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/index'   => __DIR__ . '/../view/error/index.phtml',
        ],
    ],
];
