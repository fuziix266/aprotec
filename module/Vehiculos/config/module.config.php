<?php

namespace Vehiculos;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'vehiculos-qr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/qr/:uuid',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'vehiculos-qr-solicitar-correo' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/qr/:uuid/solicitar-correo',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'solicitar-correo',
                    ],
                ],
            ],
            'vehiculos-qr-confirmar' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/qr/:uuid/confirmar',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'confirmar',
                    ],
                ],
            ],
            'vehiculos-qr-formulario' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/qr/:uuid/formulario',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'formulario',
                    ],
                ],
            ],
            'vehiculos-qr-guardar-datos' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/qr/:uuid/guardar-datos',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'guardar-datos',
                    ],
                ],
            ],
            'vehiculos-qr-consultar' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/qr/:uuid/consultar',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'consultar',
                    ],
                ],
            ],

            // Rutas de Edición
            'vehiculos-editar' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/editar',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'vehiculos-editar-solicitar-codigo' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/editar/solicitar-codigo',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'solicitar-codigo',
                    ],
                ],
            ],
            'vehiculos-editar-validar-codigo' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/editar/validar-codigo',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'validar-codigo',
                    ],
                ],
            ],
            'vehiculos-editar-formulario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/editar/formulario',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'formulario',
                    ],
                ],
            ],
            'vehiculos-editar-guardar' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/editar/guardar',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'guardar',
                    ],
                ],
            ],

            // Rutas de Autenticación
            'auth' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/auth[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'vehiculos-login' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'vehiculos-logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'logout',
                    ],
                ],
            ],

            // Rutas de Inspector
            'vehiculos-inspector-qr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/inspector/qr/:uuid',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\QrController::class,
                        'action' => 'inspector-ver',
                    ],
                ],
            ],

            // Rutas de Administración
            'vehiculos-admin' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        // Cambiado a 'gestion' para usar la nueva acción por defecto
                        'action' => 'gestion',
                    ],
                ],
            ],
            'vehiculos-admin-generar-lote' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/generar-lote',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'generar-lote',
                    ],
                ],
            ],
            'vehiculos-admin-exportar-qr-existentes' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/exportar-qr-existentes',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'exportar-qr-existentes',
                    ],
                ],
            ],
            'vehiculos-admin-cambiar-estado' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/cambiar-estado',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'cambiar-estado',
                    ],
                ],
            ],
            'vehiculos-admin-eliminar-qr' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/eliminar-qr',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'eliminar-qr',
                    ],
                ],
            ],
            'vehiculos-admin-obtener-datos' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/obtener-datos',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'obtener-datos',
                    ],
                ],
            ],
            'vehiculos-admin-guardar-edicion' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/guardar-edicion',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'guardar-edicion',
                    ],
                ],
            ],
            'vehiculos-admin-logs' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/admin/logs[/:id]',
                    'constraints' => [
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'logs',
                    ],
                ],
            ],
            'vehiculos-admin-usuarios' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/usuarios',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'usuarios',
                    ],
                ],
            ],
            'vehiculos-admin-qr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/Vehiculos/admin/qr/:uuid',
                    'constraints' => [
                        'uuid' => '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}',
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'generar-qr',
                    ],
                ],
            ],
            'vehiculos-admin-guardar-usuario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/guardar-usuario',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'guardar-usuario',
                    ],
                ],
            ],
            'vehiculos-admin-obtener-usuario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/obtener-usuario',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'obtener-usuario',
                    ],
                ],
            ],
            'vehiculos-admin-cambiar-estado-usuario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/Vehiculos/admin/cambiar-estado-usuario',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'cambiar-estado-usuario',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'vehiculos-qr' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'template_map' => [
            'layout/layout_vehiculos' => __DIR__ . '/../../Application/view/layout/layout_vehiculos.phtml',
        ],
    ],
];
