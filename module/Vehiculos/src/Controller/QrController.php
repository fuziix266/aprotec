<?php

namespace Vehiculos\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Vehiculos\Service\QrService;
use Vehiculos\Service\CorreoService;
use Vehiculos\Service\QrLogService;
use Vehiculos\Service\QrHistorialService;
use Vehiculos\Service\AuthService;

class QrController extends AbstractActionController
{
    private QrService $qrService;
    private CorreoService $correoService;
    private QrLogService $logService;
    private QrHistorialService $historialService;

    public function __construct(
        QrService $qrService,
        CorreoService $correoService,
        QrLogService $logService,
        QrHistorialService $historialService
    ) {
        $this->qrService = $qrService;
        $this->correoService = $correoService;
        $this->logService = $logService;
        $this->historialService = $historialService;
    }

    /**
     * Vista inicial al escanear QR
     */
    public function indexAction()
    {
        $uuid = $this->params()->fromRoute('uuid');
        $qr = $this->qrService->buscarPorUuid($uuid);

        if (!$qr) {
            return new ViewModel(['error' => 'Código QR no encontrado']);
        }

        $registro = $this->qrService->obtenerRegistroPorQrId($qr['id']);

        $view = new ViewModel([
            'qr' => $qr,
            'registro' => $registro,
            'uuid' => $uuid,
        ]);

        $view->setTemplate('vehiculos/qr/index');
        return $view;
    }

    /**
     * Solicitar correo para registro inicial
     */
    public function solicitarCorreoAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos', ['uuid' => $this->params()->fromRoute('uuid')]);
        }

        $uuid = $this->params()->fromRoute('uuid');
        $correo = $this->getRequest()->getPost('correo');

        // Validar dominio
        if (!str_ends_with($correo, '@aprotec.cl')) {
            return new JsonModel([
                'success' => false,
                'error' => 'Solo se permiten correos @aprotec.cl'
            ]);
        }

        $qr = $this->qrService->buscarPorUuid($uuid);
        if (!$qr) {
            return new JsonModel(['success' => false, 'error' => 'QR no encontrado']);
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $registro = $this->qrService->crearRegistroInicial($qr['id'], $correo, $ip);

        // Enviar código por email
        $enviado = $this->correoService->enviarCodigoConfirmacion($correo, $registro['codigo_confirmacion']);

        // Registrar log (sin GPS para registro inicial)
        $this->logService->registrarEvento($qr['id'], 'REGISTRO_INICIAL', null, null, $ip);

        return new JsonModel([
            'success' => $enviado,
            'message' => $enviado ? 'Código enviado al correo' : 'Error al enviar código'
        ]);
    }

    /**
     * Confirmar código de verificación
     */
    public function confirmarAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos', ['uuid' => $this->params()->fromRoute('uuid')]);
        }

        $uuid = $this->params()->fromRoute('uuid');
        $codigo = $this->getRequest()->getPost('codigo');

        $qr = $this->qrService->buscarPorUuid($uuid);
        if (!$qr) {
            return new JsonModel(['success' => false, 'error' => 'QR no encontrado']);
        }

        $resultado = $this->qrService->confirmarCodigo($qr['id'], $codigo);

        return new JsonModel($resultado);
    }

    /**
     * Mostrar formulario de datos
     */
    public function formularioAction()
    {
        $uuid = $this->params()->fromRoute('uuid');
        $datos = $this->qrService->obtenerDatosCompletos($uuid);

        if (!$datos || !$datos['registro'] || $datos['registro']['correo_confirmado'] != 1) {
            return $this->redirect()->toRoute('vehiculos', ['uuid' => $uuid]);
        }

        $view = new ViewModel($datos);
        $view->setTemplate('vehiculos/qr/formulario');
        return $view;
    }

    /**
     * Guardar datos del funcionario y vehículo
     */
    public function guardarDatosAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos', ['uuid' => $this->params()->fromRoute('uuid')]);
        }

        $uuid = $this->params()->fromRoute('uuid');
        $qr = $this->qrService->buscarPorUuid($uuid);

        if (!$qr) {
            return new JsonModel(['success' => false, 'error' => 'QR no encontrado']);
        }

        $registro = $this->qrService->obtenerRegistroPorQrId($qr['id']);
        if (!$registro || $registro['correo_confirmado'] != 1) {
            return new JsonModel(['success' => false, 'error' => 'Correo no confirmado']);
        }

        $datos = $this->getRequest()->getPost()->toArray();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $guardado = $this->qrService->guardarDatos($qr['id'], $datos, $ip, $registro['correo_funcionario']);

        return new JsonModel([
            'success' => $guardado,
            'message' => $guardado ? 'Datos guardados correctamente' : 'Error al guardar datos'
        ]);
    }

    /**
     * Consulta pública al escanear el QR.
     * Acepta GET (al abrir URL directamente) y POST (via JS).
     * - Si el usuario es inspector logueado → redirige a inspector-ver.
     * - Si es usuario público → muestra vista pública con estado del vehículo.
     * - GPS puede venir en query string (GET) o en body (POST).
     */
    public function consultarAction()
    {
        $uuid = $this->params()->fromRoute('uuid');

        // ── 1. Verificar si hay sesión de inspector ─────────────────────────
        $container = new \Laminas\Session\Container('vehiculos_qr_auth');
        if (isset($container->user_id)) {
            // Inspector logueado: registrar log y redirigir a vista completa
            $qrInspector = $this->qrService->buscarPorUuid($uuid);
            if ($qrInspector) {
                // Obtener GPS (GET o POST)
                $lat = $this->params()->fromQuery('lat') ?? $this->getRequest()->getPost('lat');
                $lon = $this->params()->fromQuery('lon') ?? $this->getRequest()->getPost('lon');
                $accuracy = $this->params()->fromQuery('accuracy') ?? $this->getRequest()->getPost('accuracy');

                if ($lat && $lon) {
                    $this->logService->registrarEvento(
                        $qrInspector['id'],
                        'CONSULTA_INSPECTOR',
                        ['lat' => $lat, 'lon' => $lon, 'accuracy' => $accuracy],
                        $container->user_id
                    );
                }
            }
            return $this->redirect()->toRoute('vehiculos-inspector-qr', ['uuid' => $uuid]);
        }

        // ── 2. Usuario público ───────────────────────────────────────────────

        // Obtener GPS de query string (GET) o body (POST)
        $lat = $this->params()->fromQuery('lat') ?? $this->getRequest()->getPost('lat');
        $lon = $this->params()->fromQuery('lon') ?? $this->getRequest()->getPost('lon');
        $accuracy = $this->params()->fromQuery('accuracy') ?? $this->getRequest()->getPost('accuracy');

        // ── 3. GPS ausente: mostrar vista informativa ────────────────────────
        if (!$lat || !$lon) {
            $view = new ViewModel(['uuid' => $uuid]);
            $view->setTemplate('vehiculos/qr/sin-gps');
            return $view;
        }

        // ── 4. Buscar QR en BD ────────────────────────────────────────────────
        $qr = $this->qrService->buscarPorUuid($uuid);
        if (!$qr) {
            $view = new ViewModel(['uuid' => $uuid, 'error' => 'Código QR no encontrado o inválido.']);
            $view->setTemplate('vehiculos/qr/consulta-publica');
            return $view;
        }

        $registro = $this->qrService->obtenerRegistroPorQrId($qr['id']);

        // ── 5. Registrar escaneo con GPS ──────────────────────────────────────
        $this->logService->registrarEvento(
            $qr['id'],
            'CONSULTA_PUBLICA',
            ['lat' => $lat, 'lon' => $lon, 'accuracy' => $accuracy]
        );

        // ── 6. Mostrar vista pública con formato ──────────────────────────────
        $view = new ViewModel([
            'qr'       => $qr,
            'registro' => $registro,
            'lat'      => $lat,
            'lon'      => $lon,
        ]);
        $view->setTemplate('vehiculos/qr/consulta-publica');
        return $view;
    }

    /**
     * Vista para inspectores (con GPS, muestra todos los datos)
     */
    public function inspectorVerAction()
    {
        // Verificar autenticación
        $container = new \Laminas\Session\Container('vehiculos_qr_auth');
        if (!isset($container->user_id)) {
            return $this->redirect()->toRoute('vehiculos-login');
        }

        $uuid = $this->params()->fromRoute('uuid');
        $datos = $this->qrService->obtenerDatosCompletos($uuid);

        if (!$datos) {
            return new ViewModel(['error' => 'QR no encontrado']);
        }

        // Si es POST con GPS, registrar log
        if ($this->getRequest()->isPost()) {
            $gps = $this->getRequest()->getPost('gps');
            if ($gps && isset($gps['lat']) && isset($gps['lon'])) {
                $this->logService->registrarEvento($datos['qr']['id'], 'CONSULTA_INSPECTOR', [
                    'lat' => $gps['lat'],
                    'lon' => $gps['lon'],
                    'accuracy' => $gps['accuracy'] ?? null
                ], $container->user_id);
            }
        }

        $view = new ViewModel($datos);
        $view->setTemplate('vehiculos/qr/inspector-ver');
        return $view;
    }
}
