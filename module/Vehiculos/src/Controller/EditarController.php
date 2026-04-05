<?php

namespace Vehiculos\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Vehiculos\Service\QrService;
use Vehiculos\Service\CorreoService;
use Vehiculos\Service\QrHistorialService;
use Laminas\Session\Container as SessionContainer;

class EditarController extends AbstractActionController
{
    private QrService $qrService;
    private CorreoService $correoService;
    private QrHistorialService $historialService;

    public function __construct(
        QrService $qrService,
        CorreoService $correoService,
        QrHistorialService $historialService
    ) {
        $this->qrService = $qrService;
        $this->correoService = $correoService;
        $this->historialService = $historialService;
    }

    /**
     * Formulario para ingresar correo
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $view->setTemplate('vehiculos/editar/index');
        return $view;
    }

    /**
     * Solicitar código de edición
     */
    public function solicitarCodigoAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos-editar');
        }

        $correo = $this->getRequest()->getPost('correo');

        // Validar dominio
        if (!str_ends_with($correo, '@aprotec.cl')) {
            return new JsonModel([
                'success' => false,
                'error' => 'Solo se permiten correos @aprotec.cl'
            ]);
        }

        // Buscar registro por correo
        $registro = $this->qrService->obtenerRegistroPorCorreo($correo);
        if (!$registro || $registro['correo_confirmado'] != 1) {
            return new JsonModel([
                'success' => false,
                'error' => 'No se encontró un registro confirmado con ese correo'
            ]);
        }

        // Generar código
        $codigo = $this->qrService->generarCodigoConfirmacion();
        $expira = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // Actualizar registro con el código
        $this->qrService->actualizarCodigoEdicion($registro['id'], $codigo, $expira);

        // Enviar código por email
        $enviado = $this->correoService->enviarCodigoConfirmacion($correo, $codigo);

        // Guardar correo en sesión temporal
        $session = new SessionContainer('vehiculos_qr_edit');
        $session->correo = $correo;
        $session->registro_id = $registro['id'];

        return new JsonModel([
            'success' => $enviado,
            'message' => $enviado ? 'Código enviado al correo' : 'Error al enviar código'
        ]);
    }

    /**
     * Validar código y habilitar edición
     */
    public function validarCodigoAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos-editar');
        }

        $session = new SessionContainer('vehiculos_qr_edit');
        if (!isset($session->correo) || !isset($session->registro_id)) {
            return new JsonModel(['success' => false, 'error' => 'Sesión expirada']);
        }

        $codigo = $this->getRequest()->getPost('codigo');
        $registro = $this->qrService->obtenerRegistroPorId($session->registro_id);

        if (!$registro) {
            return new JsonModel(['success' => false, 'error' => 'Registro no encontrado']);
        }

        if ($registro['codigo_confirmacion'] !== $codigo) {
            return new JsonModel(['success' => false, 'error' => 'Código incorrecto']);
        }

        $expira = strtotime($registro['codigo_confirmacion_expira']);
        if (time() > $expira) {
            return new JsonModel(['success' => false, 'error' => 'El código ha expirado']);
        }

        // Código válido, marcar sesión como autenticada para edición
        $session->codigo_validado = true;

        return new JsonModel(['success' => true]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function formularioAction()
    {
        $session = new SessionContainer('vehiculos_qr_edit');
        if (!isset($session->codigo_validado) || !$session->codigo_validado) {
            return $this->redirect()->toRoute('vehiculos-editar');
        }

        $registro = $this->qrService->obtenerRegistroPorId($session->registro_id);

        $view = new ViewModel(['registro' => $registro]);
        $view->setTemplate('vehiculos/editar/formulario');
        return $view;
    }

    /**
     * Guardar cambios
     */
    public function guardarAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('vehiculos-editar');
        }

        $session = new SessionContainer('vehiculos_qr_edit');
        if (!isset($session->codigo_validado) || !$session->codigo_validado) {
            return new JsonModel(['success' => false, 'error' => 'No autorizado']);
        }

        $registro = $this->qrService->obtenerRegistroPorId($session->registro_id);
        if (!$registro) {
            return new JsonModel(['success' => false, 'error' => 'Registro no encontrado']);
        }

        $datos = $this->getRequest()->getPost()->toArray();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // El correo no se puede editar, por seguridad
        unset($datos['correo_funcionario']);

        $qr = $this->qrService->obtenerQrPorRegistroId($registro['id']);
        $guardado = $this->qrService->guardarDatos($qr['id'], $datos, $ip, $session->correo);

        if ($guardado) {
            // Limpiar sesión
            $session->getManager()->destroy();
        }

        return new JsonModel([
            'success' => $guardado,
            'message' => $guardado ? 'Datos actualizados correctamente' : 'Error al actualizar datos'
        ]);
    }
}
