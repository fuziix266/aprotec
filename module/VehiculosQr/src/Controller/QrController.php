<?php

namespace VehiculosQr\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use VehiculosQr\Service\QrService;
use VehiculosQr\Service\CorreoService;
use VehiculosQr\Service\QrLogService;
use VehiculosQr\Service\QrHistorialService;
use VehiculosQr\Service\AuthService;

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
        
        $view->setTemplate('vehiculos-qr/qr/index');
        return $view;
    }

    /**
     * Solicitar correo para registro inicial
     */
    public function solicitarCorreoAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos-qr', ['uuid' => $this->params()->fromRoute('uuid')]);
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
            return $this->redirect()->toRoute('vehiculos-qr', ['uuid' => $this->params()->fromRoute('uuid')]);
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
            return $this->redirect()->toRoute('vehiculos-qr', ['uuid' => $uuid]);
        }
        
        $view = new ViewModel($datos);
        $view->setTemplate('vehiculos-qr/qr/formulario');
        return $view;
    }

    /**
     * Guardar datos del funcionario y vehículo
     */
    public function guardarDatosAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos-qr', ['uuid' => $this->params()->fromRoute('uuid')]);
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
     * Consulta pública (con GPS, muestra solo patente)
     */
    public function consultarAction()
    {
        if (!$this->getRequest()->isPost()) {
            return new JsonModel(['success' => false, 'error' => 'Método no permitido']);
        }
        
        $uuid = $this->params()->fromRoute('uuid');
        $gps = $this->getRequest()->getPost('gps');
        
        // Validar GPS
        if (!isset($gps['lat']) || !isset($gps['lon'])) {
            return new JsonModel(['success' => false, 'error' => 'GPS requerido', 'requiere_gps' => true]);
        }
        
        $qr = $this->qrService->buscarPorUuid($uuid);
        if (!$qr) {
            return new JsonModel(['success' => false, 'error' => 'QR no encontrado']);
        }
        
        $registro = $this->qrService->obtenerRegistroPorQrId($qr['id']);
        
        // Registrar log con GPS
        $this->logService->registrarEvento($qr['id'], 'CONSULTA_PUBLICA', [
            'lat' => $gps['lat'],
            'lon' => $gps['lon'],
            'accuracy' => $gps['accuracy'] ?? null
        ]);
        
        return new JsonModel([
            'success' => true,
            'estado' => $qr['estado'],
            'patente' => $registro['patente'] ?? 'Sin patente',
            'habilitado' => $qr['estado'] === 'ASIGNADO'
        ]);
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
        $view->setTemplate('vehiculos-qr/qr/inspector-ver');
        return $view;
    }
}

