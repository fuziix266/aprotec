<?php

namespace Vehiculos;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\EventManager\EventInterface;
use Laminas\Mvc\MvcEvent;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    public function onBootstrap(EventInterface $e): void
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        // Cambiar layout cuando se despacha un controller de Vehiculos
        $sharedEventManager->attach(
            'Laminas\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            function (MvcEvent $event) {
                $controller = $event->getTarget();
                $controllerClass = get_class($controller);

                // Solo si el controller pertenece al namespace Vehiculos
                if (str_starts_with($controllerClass, 'Vehiculos\\')) {
                    $event->getViewModel()->setTemplate('layout/layout_vehiculos');
                }
            },
            100
        );
    }

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                // TableGateways
                'QrCodigosTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('qr_codigos', $dbAdapter, null, $resultSetPrototype);
                },
                'QrRegistrosTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('qr_registros', $dbAdapter, null, $resultSetPrototype);
                },
                'QrRegistrosHistorialTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('qr_registros_historial', $dbAdapter, null, $resultSetPrototype);
                },
                'QrUsuariosTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('qr_usuarios', $dbAdapter, null, $resultSetPrototype);
                },
                'QrLogsTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    return new TableGateway('qr_logs', $dbAdapter, null, $resultSetPrototype);
                },

                // Repositories
                Repository\QrCodigosRepository::class => function ($container) {
                    return new Repository\QrCodigosRepository(
                        $container->get('QrCodigosTableGateway')
                    );
                },
                Repository\QrRegistrosRepository::class => function ($container) {
                    return new Repository\QrRegistrosRepository(
                        $container->get('QrRegistrosTableGateway')
                    );
                },
                Repository\QrHistorialRepository::class => function ($container) {
                    return new Repository\QrHistorialRepository(
                        $container->get('QrRegistrosHistorialTableGateway')
                    );
                },
                Repository\QrUsuariosRepository::class => function ($container) {
                    return new Repository\QrUsuariosRepository(
                        $container->get('QrUsuariosTableGateway')
                    );
                },
                Repository\QrLogsRepository::class => function ($container) {
                    return new Repository\QrLogsRepository(
                        $container->get('QrLogsTableGateway')
                    );
                },

                // Services
                Service\QrService::class => function ($container) {
                    return new Service\QrService(
                        $container->get(Repository\QrCodigosRepository::class),
                        $container->get(Repository\QrRegistrosRepository::class),
                        $container->get(Repository\QrHistorialRepository::class)
                    );
                },
                Service\CorreoService::class => function ($container) {
                    $config = $container->get('config');
                    $appConfig = $config['app_config'] ?? [];
                    $smtpConfig = $appConfig['smtp'] ?? [];
                    // Pasar site_url al servicio para usarlo en los correos
                    $smtpConfig['site_url'] = $appConfig['site_url'] ?? 'www.aprotec.cl';
                    return new Service\CorreoService($smtpConfig);
                },
                Service\AuthService::class => function ($container) {
                    return new Service\AuthService(
                        $container->get(Repository\QrUsuariosRepository::class)
                    );
                },
                Service\QrLogService::class => function ($container) {
                    return new Service\QrLogService(
                        $container->get(Repository\QrLogsRepository::class)
                    );
                },
                Service\QrHistorialService::class => function ($container) {
                    return new Service\QrHistorialService(
                        $container->get(Repository\QrHistorialRepository::class)
                    );
                },
            ],
        ];
    }

    public function getControllerConfig(): array
    {
        return [
            'factories' => [
                Controller\QrController::class => function ($container) {
                    return new Controller\QrController(
                        $container->get(Service\QrService::class),
                        $container->get(Service\CorreoService::class),
                        $container->get(Service\QrLogService::class),
                        $container->get(Service\QrHistorialService::class)
                    );
                },
                Controller\AdminController::class => function ($container) {
                    $config = $container->get('config');
                    return new Controller\AdminController(
                        $container->get(Service\QrService::class),
                        $container->get(Service\QrLogService::class),
                        $container->get(Service\AuthService::class),
                        $container->get(Repository\QrUsuariosRepository::class),
                        $config['app_config'] ?? []
                    );
                },
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController(
                        $container->get(Service\AuthService::class)
                    );
                },
                Controller\EditarController::class => function ($container) {
                    return new Controller\EditarController(
                        $container->get(Service\QrService::class),
                        $container->get(Service\CorreoService::class),
                        $container->get(Service\QrHistorialService::class)
                    );
                },
            ],
        ];
    }
}
