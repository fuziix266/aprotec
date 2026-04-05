<?php

namespace VehiculosQr;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'vehiculos-qr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/vehiculosqr/qr/:uuid',
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
                    'route' => '/vehiculosqr/qr/:uuid/solicitar-correo',
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
                    'route' => '/vehiculosqr/qr/:uuid/confirmar',
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
                    'route' => '/vehiculosqr/qr/:uuid/formulario',
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
                    'route' => '/vehiculosqr/qr/:uuid/guardar-datos',
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
                    'route' => '/vehiculosqr/qr/:uuid/consultar',
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
                    'route' => '/vehiculosqr/editar',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'vehiculos-editar-solicitar-codigo' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/editar/solicitar-codigo',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'solicitar-codigo',
                    ],
                ],
            ],
            'vehiculos-editar-validar-codigo' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/editar/validar-codigo',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'validar-codigo',
                    ],
                ],
            ],
            'vehiculos-editar-formulario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/editar/formulario',
                    'defaults' => [
                        'controller' => Controller\EditarController::class,
                        'action' => 'formulario',
                    ],
                ],
            ],
            'vehiculos-editar-guardar' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/editar/guardar',
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
                    'route' => '/vehiculosqr/auth[/:action]',
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
                    'route' => '/vehiculosqr/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'vehiculos-logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/logout',
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
                    'route' => '/vehiculosqr/inspector/qr/:uuid',
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
                    'route' => '/vehiculosqr/admin',
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
                    'route' => '/vehiculosqr/admin/generar-lote',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'generar-lote',
                    ],
                ],
            ],
            'vehiculos-admin-exportar-qr-existentes' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/exportar-qr-existentes',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'exportar-qr-existentes',
                    ],
                ],
            ],
            'vehiculos-admin-cambiar-estado' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/cambiar-estado',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'cambiar-estado',
                    ],
                ],
            ],
            'vehiculos-admin-eliminar-qr' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/eliminar-qr',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'eliminar-qr',
                    ],
                ],
            ],
            'vehiculos-admin-obtener-datos' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/obtener-datos',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'obtener-datos',
                    ],
                ],
            ],
            'vehiculos-admin-guardar-edicion' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/guardar-edicion',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'guardar-edicion',
                    ],
                ],
            ],
            'vehiculos-admin-logs' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/logs[/:id]',
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
                    'route' => '/vehiculosqr/admin/usuarios',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'usuarios',
                    ],
                ],
            ],
            'vehiculos-admin-qr' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/qr/:uuid',
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
                    'route' => '/vehiculosqr/admin/guardar-usuario',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'guardar-usuario',
                    ],
                ],
            ],
            'vehiculos-admin-obtener-usuario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/obtener-usuario',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'obtener-usuario',
                    ],
                ],
            ],
            'vehiculos-admin-cambiar-estado-usuario' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/vehiculosqr/admin/cambiar-estado-usuario',
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

