<?php

namespace VehiculosQr\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use VehiculosQr\Service\QrService;
use VehiculosQr\Service\QrLogService;
use VehiculosQr\Service\AuthService;
use VehiculosQr\Repository\QrUsuariosRepository;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class AdminController extends AbstractActionController
{
    private QrService $qrService;
    private QrLogService $logService;
    private AuthService $authService;
    private QrUsuariosRepository $usuariosRepository;
    private array $appConfig;

    public function __construct(
        QrService $qrService,
        QrLogService $logService,
        AuthService $authService,
        QrUsuariosRepository $usuariosRepository,
        array $appConfig
    ) {
        $this->qrService = $qrService;
        $this->logService = $logService;
        $this->authService = $authService;
        $this->usuariosRepository = $usuariosRepository;
        $this->appConfig = $appConfig;
    }

    /**
     * Verificar autenticación antes de cada acción
     * Excepciones: generarQrAction (endpoint público para generar imágenes)
     */
    public function onDispatch(\Laminas\Mvc\MvcEvent $e)
    {
        $action = $this->params()->fromRoute('action', 'index');

        // Acciones públicas que no requieren autenticación
        $publicActions = ['generar-qr'];

        if (!in_array($action, $publicActions)) {
            if (!$this->authService->isAuthenticated() || !$this->authService->isAdmin()) {
                return $this->redirect()->toRoute('vehiculos-login');
            }
        }

        return parent::onDispatch($e);
    }


    public function indexAction()
    {
        $this->layout()->setVariable('heroTitle', 'Panel de Administración');
        $this->layout()->setVariable('heroSubtitle', 'Lista de códigos registrados.');
    }
    /**
     * Listado de códigos QR
     */
    public function gestionAction()
    {
        $this->layout()->setVariable('visible-hero', 0);

        $page = (int) $this->params()->fromQuery('page', 1);
        $limit = 20;

        $codigos = $this->qrService->listarQrPaginado($page, $limit);
        $total = $this->qrService->contarQr();
        $totalPages = ceil($total / $limit);

        // Obtener registros asociados
        foreach ($codigos as &$codigo) {
            $codigo['registro'] = $this->qrService->obtenerRegistroPorQrId($codigo['id']);
        }

        $view = new ViewModel([
            'codigos' => $codigos,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'usuario' => $this->authService->getCurrentUser(),
        ]);

        return $view;
    }

    /**
     * Generar lote de códigos QR
     */
    public function generarLoteAction()
    {

        $this->layout()->setVariable('visible-hero', 0);

        if ($this->getRequest()->isPost()) {
            $cantidad = (int) $this->getRequest()->getPost('cantidad', 1);



            if ($cantidad < 1 || $cantidad > 1000) {
                return new JsonModel([
                    'success' => false,
                    'error' => 'La cantidad debe estar entre 1 y 1000'
                ]);
            }

            $codigos = $this->qrService->generarLoteQr($cantidad);


            // Generar PDF con los QR

            $pdfUrl = $this->generarPdfConQr($codigos);


            return new JsonModel([
                'success' => true,
                'cantidad' => count($codigos),
                'pdf_url' => $pdfUrl,
                'message' => "Se generaron {$cantidad} códigos QR exitosamente"
            ]);
        }

        $view = new ViewModel([
            'usuario' => $this->authService->getCurrentUser(),
        ]);
        $view->setTemplate('vehiculos-qr/admin/generar-lote');
        return $view;
    }

    /**
     * Cambiar estado de un QR
     */
    public function cambiarEstadoAction()
    {
        if (!$this->getRequest()->isPost()) {
            return new JsonModel(['success' => false, 'error' => 'Método no permitido']);
        }

        $uuid = $this->getRequest()->getPost('uuid');
        $nuevoEstado = $this->getRequest()->getPost('estado');

        // Validar que se reciban los parámetros requeridos
        if (empty($uuid) || empty($nuevoEstado)) {
            return new JsonModel([
                'success' => false,
                'error' => 'Faltan parámetros requeridos (uuid, estado)'
            ]);
        }

        // Buscar el QR por UUID
        $qr = $this->qrService->buscarPorUuid($uuid);

        if (!$qr) {
            return new JsonModel([
                'success' => false,
                'error' => 'Código QR no encontrado'
            ]);
        }

        // Validar el estado
        $estadosValidos = ['PENDIENTE', 'HABILITADO', 'DESHABILITADO'];
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return new JsonModel([
                'success' => false,
                'error' => "Estado no válido. Estados permitidos: " . implode(', ', $estadosValidos)
            ]);
        }

        // Cambiar el estado
        $cambiado = $this->qrService->cambiarEstado($qr['id'], $nuevoEstado);

        if ($cambiado) {
            // Registrar en el log
            $usuarioActual = $this->authService->getCurrentUser();
            error_log("Admin '{$usuarioActual['nombre']}' cambió estado del QR '{$uuid}' a '{$nuevoEstado}'");

            return new JsonModel([
                'success' => true,
                'message' => "Estado cambiado a {$nuevoEstado} correctamente"
            ]);
        }

        return new JsonModel([
            'success' => false,
            'error' => 'Error al cambiar el estado en la base de datos'
        ]);
    }

    /**
     * Generar imagen PNG del QR para un UUID (devuelve image/png)
     */
    public function generarQrAction()
    {
        // Limpiar cualquier output previo que pueda contaminar la imagen
        while (ob_get_level()) {
            ob_end_clean();
        }

        $uuid = $this->params()->fromRoute('uuid') ?: $this->params()->fromQuery('uuid');

        if (empty($uuid)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            $response->setContent('UUID requerido');
            return $response;
        }

        try {
            $url = 'https://www.didecoarica.cl/vehiculos/qr/' . $uuid;

            // Generar QR usando Endroid/QrCode
            $qrResult = Builder::create()
                ->writer(new PngWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(500)
                ->margin(0)
                ->build();

            // Obtener el contenido PNG usando getDataUri y extraer el base64
            $dataUri = $qrResult->getDataUri();

            // Extraer el contenido base64 del data URI (formato: data:image/png;base64,XXXXX)
            if (preg_match('/^data:image\/png;base64,(.+)$/', $dataUri, $matches)) {
                $png = base64_decode($matches[1]);
            } else {
                throw new \RuntimeException('No se pudo extraer imagen del data URI');
            }

            // Configurar respuesta
            $response = $this->getResponse();
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'image/png');
            $headers->addHeaderLine('Content-Length', strlen($png));
            $headers->addHeaderLine('Cache-Control', 'public, max-age=86400'); // Cache por 24h
            $response->setContent($png);

            return $response;
        } catch (\Throwable $e) {
            error_log('Error generando QR local para UUID ' . $uuid . ': ' . $e->getMessage());
            error_log('Trace: ' . $e->getTraceAsString());

            $response = $this->getResponse();
            $response->setStatusCode(500);
            $response->setContent('Error generando QR: ' . $e->getMessage());
            return $response;
        }
    }

    /**
     * Regenerar PDF de códigos QR seleccionados
     */
    public function regenerarPdfAction()
    {
        if (!$this->getRequest()->isPost()) {
            return new JsonModel(['success' => false, 'error' => 'Método no permitido']);
        }

        $ids = $this->getRequest()->getPost('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return new JsonModel([
                'success' => false,
                'error' => 'Debe seleccionar al menos un código QR'
            ]);
        }

        // Obtener códigos por IDs
        $codigos = [];
        foreach ($ids as $id) {
            $codigo = $this->qrService->buscarPorId((int)$id);
            if ($codigo) {
                $codigos[] = $codigo;
            }
        }

        if (empty($codigos)) {
            return new JsonModel([
                'success' => false,
                'error' => 'No se encontraron códigos válidos'
            ]);
        }

        $cantidad = count($codigos);

        // Generar PDF
        $pdfUrl = $this->generarPdfConQr($codigos);

        return new JsonModel([
            'success' => true,
            'cantidad' => $cantidad,
            'pdf_url' => $pdfUrl,
            'message' => "PDF generado con {$cantidad} código(s) QR"
        ]);
    }

    /**
     * Ver logs de un QR específico
     */
    public function logsAction()
    {
        $id = (int) $this->params()->fromRoute('id');

        $qr = $this->qrService->buscarPorUuid($this->params()->fromRoute('id'));
        if (!$qr) {
            $qr = ['id' => $id]; // Buscar por ID numérico como fallback
        }

        $logs = $this->logService->obtenerLogsPorQr($id, 200);
        $sospechosos = $this->logService->obtenerEscaneosSospechosos($id);
        $registro = $this->qrService->obtenerRegistroPorQrId($id);

        $view = new ViewModel([
            'qr' => $qr,
            'registro' => $registro,
            'logs' => $logs,
            'sospechosos' => $sospechosos,
            'usuario' => $this->authService->getCurrentUser(),
        ]);

        $view->setTemplate('vehiculos-qr/admin/logs');
        return $view;
    }

    /**
     * Administración de usuarios del sistema
     */
    public function usuariosAction()
    {
        // Mantener variables del layout y redirigir a la acción 'gestion'
        $this->layout()->setVariable('heroTitle', 'Administración de Usuarios');

        $this->layout()->setVariable('visible-hero', 1);

        $usuarios = $this->usuariosRepository->findAll();

        return new ViewModel([
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Obtener datos de un registro QR para edición
     */
    public function obtenerDatosAction()
    {
        $uuid = $this->params()->fromQuery('uuid');

        if (empty($uuid)) {
            return new JsonModel([
                'success' => false,
                'error' => 'UUID no proporcionado'
            ]);
        }

        try {
            // Buscar QR por UUID
            $qr = $this->qrService->buscarPorUuid($uuid);

            if (!$qr) {
                return new JsonModel([
                    'success' => false,
                    'error' => 'Código QR no encontrado'
                ]);
            }

            // Obtener registro asociado
            $registro = $this->qrService->obtenerRegistroPorQrId($qr['id']);

            return new JsonModel([
                'success' => true,
                'qr' => $qr,
                'registro' => $registro
            ]);
        } catch (\Exception $e) {
            error_log("Error al obtener datos del QR: " . $e->getMessage());
            return new JsonModel([
                'success' => false,
                'error' => 'Error al obtener los datos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Guardar edición de datos del registro (solo admin)
     */
    public function guardarEdicionAction()
    {
        if (!$this->getRequest()->isPost()) {
            return new JsonModel(['success' => false, 'error' => 'Método no permitido']);
        }

        $uuid = $this->getRequest()->getPost('uuid');
        $correoFuncionario = $this->getRequest()->getPost('correo_funcionario');
        $nombres = $this->getRequest()->getPost('nombres');
        $apellidos = $this->getRequest()->getPost('apellidos');
        $celular = $this->getRequest()->getPost('celular');

        // Validar campos obligatorios
        if (empty($uuid) || empty($correoFuncionario) || empty($nombres) || empty($apellidos) || empty($celular)) {
            return new JsonModel([
                'success' => false,
                'error' => 'Faltan campos obligatorios (uuid, correo, nombres, apellidos, celular)'
            ]);
        }

        // Validar dominio del correo
        if (!preg_match('/@municipalidadarica\.cl$/i', $correoFuncionario)) {
            return new JsonModel([
                'success' => false,
                'error' => 'El correo debe ser del dominio @municipalidadarica.cl'
            ]);
        }

        try {
            // Buscar QR por UUID
            $qr = $this->qrService->buscarPorUuid($uuid);

            if (!$qr) {
                return new JsonModel([
                    'success' => false,
                    'error' => 'Código QR no encontrado'
                ]);
            }

            // Obtener registro actual
            $registroActual = $this->qrService->obtenerRegistroPorQrId($qr['id']);

            // Preparar datos actualizados
            $datosActualizados = [
                'correo_funcionario' => trim($correoFuncionario),
                'nombres' => trim($nombres),
                'apellidos' => trim($apellidos),
                'rut' => trim($this->getRequest()->getPost('rut', '')),
                'celular' => trim($celular),
                'unidad' => trim($this->getRequest()->getPost('unidad', '')),
                'anexo' => trim($this->getRequest()->getPost('anexo', '')),
                'cargo' => trim($this->getRequest()->getPost('cargo', '')),
                'patente' => strtoupper(trim($this->getRequest()->getPost('patente', ''))),
                'observaciones' => trim($this->getRequest()->getPost('observaciones', '')),
                'fecha_actualizacion' => date('Y-m-d H:i:s'),
                'actualizado_por_ip' => $_SERVER['REMOTE_ADDR'] ?? null
            ];

            // Si no existe registro, crear uno nuevo
            if (!$registroActual) {
                $datosActualizados['qr_codigo_id'] = $qr['id'];
                $datosActualizados['correo_confirmado'] = 1;
                $datosActualizados['fecha_confirmacion'] = date('Y-m-d H:i:s');
                $datosActualizados['fecha_registro'] = date('Y-m-d H:i:s');
                $datosActualizados['creado_por_ip'] = $_SERVER['REMOTE_ADDR'] ?? null;

                $success = $this->qrService->crearRegistro($qr['id'], $datosActualizados);

                if ($success) {
                    // Actualizar estado del QR a HABILITADO
                    $this->qrService->cambiarEstado($qr['id'], 'HABILITADO');

                    $usuarioActual = $this->authService->getCurrentUser();
                    error_log("Admin '{$usuarioActual['nombre']}' creó registro para QR '{$uuid}'");

                    return new JsonModel([
                        'success' => true,
                        'message' => 'Registro creado exitosamente'
                    ]);
                }
            } else {
                // Actualizar registro existente
                $success = $this->qrService->actualizarDatos($qr['id'], $datosActualizados);

                if ($success) {
                    $usuarioActual = $this->authService->getCurrentUser();
                    error_log("Admin '{$usuarioActual['nombre']}' actualizó datos del QR '{$uuid}'");

                    return new JsonModel([
                        'success' => true,
                        'message' => 'Datos actualizados exitosamente'
                    ]);
                }
            }

            return new JsonModel([
                'success' => false,
                'error' => 'No se pudieron guardar los cambios en la base de datos'
            ]);
        } catch (\Exception $e) {
            error_log("Error al guardar edición del QR: " . $e->getMessage());
            return new JsonModel([
                'success' => false,
                'error' => 'Error al guardar los datos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generar PDF con códigos QR (57mm x 93mm en hojas A4)
     */
    private function generarPdfConQr(array $codigos): string
    {
        try {
            // Obtener ruta desde configuración
            $tempPath = $this->appConfig['temp_path'] ?? __DIR__ . '/../../../../../public/assets/temp';
            $baseUrl = $this->appConfig['base_url'] ?? '/';

            // Asegurar que el directorio existe
            if (!is_dir($tempPath)) {
                mkdir($tempPath, 0755, true);
            }



            // Crear PDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            // Configuración del documento
            $pdf->SetCreator('Municipalidad de Arica');
            $pdf->SetAuthor('Sistema de Identificación Vehicular');
            $pdf->SetTitle('Códigos QR para Vehículos');
            $pdf->SetSubject('Lote de Códigos QR');

            // Quitar header y footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Configurar márgenes mínimos
            $pdf->SetMargins(5, 5, 5);
            $pdf->SetAutoPageBreak(false);  // Desactivar salto automático

            // Dimensiones de página A4
            $pageWidth = 210;  // mm
            $pageHeight = 297; // mm
            $margen = 5;       // mm

            // Dimensiones del QR (57mm x 93mm)
            $qrWidth = 57;
            $qrHeight = 93;

            // Configuración de grilla: 3 columnas x 3 filas
            $columnas = 3;
            $filas = 3;
            $qrPorPagina = $columnas * $filas;

            // Calcular espaciado para centrar en página
            $areaUtilAncho = $pageWidth - (2 * $margen);  // 200mm
            $areaUtilAlto = $pageHeight - (2 * $margen);  // 287mm

            $espacioH = ($areaUtilAncho - ($columnas * $qrWidth)) / ($columnas + 1);
            $espacioV = ($areaUtilAlto - ($filas * $qrHeight)) / ($filas + 1);

            $contador = 0;

            foreach ($codigos as $codigo) {
                // Nueva página cada 9 QR (3x3)
                if ($contador % $qrPorPagina === 0) {
                    $pdf->AddPage();
                }

                // Calcular posición en la grilla
                $posicion = $contador % $qrPorPagina;
                $col = $posicion % $columnas;
                $fila = floor($posicion / $columnas);

                // Calcular coordenadas X, Y con espaciado correcto
                $x = $margen + $espacioH + ($col * ($qrWidth + $espacioH));
                $y = $margen + $espacioV + ($fila * ($qrHeight + $espacioV));

                // Generar imagen QR en memoria
                $url = 'https://www.didecoarica.cl/vehiculos/qr/' . $codigo['uuid_qr'];

                $qrResult = Builder::create()
                    ->writer(new PngWriter())
                    ->data($url)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                    ->size(400)
                    ->margin(5)
                    ->build();

                // Guardar temporalmente la imagen
                $qrTempFile = $tempPath . DIRECTORY_SEPARATOR . 'temp_qr_' . $codigo['id'] . '.png';
                $qrResult->saveToFile($qrTempFile);

                // Dibujar borde del diseño
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);
                $pdf->Rect($x, $y, $qrWidth, $qrHeight);

                // Logo/encabezado
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetXY($x + 2, $y + 2);
                $pdf->Cell($qrWidth - 4, 6, 'MUNICIPALIDAD DE ARICA', 0, 0, 'C');

                // Título
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetXY($x + 2, $y + 8);
                $pdf->Cell($qrWidth - 4, 5, 'Identificación Vehicular', 0, 0, 'C');

                // Imagen QR (centrada, tamaño 45x45mm)
                $qrSize = 45;
                $qrX = $x + ($qrWidth - $qrSize) / 2;
                $qrY = $y + 15;
                $pdf->Image($qrTempFile, $qrX, $qrY, $qrSize, $qrSize, 'PNG');

                // Código abajo del QR
                $pdf->SetFont('courier', 'B', 9);
                $pdf->SetXY($x + 2, $y + $qrY + $qrSize + 2);
                $pdf->Cell($qrWidth - 4, 5, substr($codigo['uuid_qr'], 0, 8), 0, 0, 'C');

                // Instrucciones
                $pdf->SetFont('helvetica', '', 6);
                $pdf->SetXY($x + 2, $y + $qrHeight - 15);
                $pdf->MultiCell($qrWidth - 4, 3, "Escanee este código QR para registrar o consultar información del vehículo.\n\nwww.didecoarica.cl", 0, 'C');

                // Eliminar archivo temporal
                @unlink($qrTempFile);

                $contador++;
            }

            // Guardar PDF
            $filename = 'qr_lote_' . date('Ymd_His') . '.pdf';
            $filepath = $tempPath . DIRECTORY_SEPARATOR . $filename;



            $pdf->Output($filepath, 'F');

            if (file_exists($filepath)) {
            } else {
                error_log("Error: El archivo PDF no se generó en: " . $filepath);
            }

            return $baseUrl . 'assets/temp/' . $filename;
        } catch (\Exception $e) {
            error_log("PDF Generator - ERROR: " . $e->getMessage());
            error_log("PDF Generator - Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Guardar nuevo usuario o editar existente
     */
    public function guardarUsuarioAction()
    {
        if (!$this->getRequest()->isPost()) {
            return new JsonModel(['success' => false, 'message' => 'Método no permitido']);
        }

        try {
            $data = $this->getRequest()->getPost();
            $id = (int) ($data['id'] ?? 0);
            $nombre = trim($data['nombre'] ?? '');
            $correo = trim($data['correo'] ?? '');
            $password = trim($data['password'] ?? '');
            $rol = $data['rol'] ?? 'INSPECTOR';
            $activo = isset($data['activo']) ? (int) $data['activo'] : 1;

            // Validaciones
            if (empty($nombre)) {
                return new JsonModel(['success' => false, 'message' => 'El nombre es obligatorio']);
            }

            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                return new JsonModel(['success' => false, 'message' => 'Correo electrónico inválido']);
            }

            if (!str_ends_with($correo, '@municipalidadarica.cl')) {
                return new JsonModel(['success' => false, 'message' => 'El correo debe ser @municipalidadarica.cl']);
            }

            if (!in_array($rol, ['ADMIN', 'INSPECTOR'])) {
                return new JsonModel(['success' => false, 'message' => 'Rol inválido']);
            }

            // Verificar si el correo ya existe (excepto si es el mismo usuario)
            $existente = $this->usuariosRepository->findByCorreo($correo);
            if ($existente && (int) $existente['id'] !== $id) {
                return new JsonModel(['success' => false, 'message' => 'El correo ya está registrado']);
            }

            $datosUsuario = [
                'nombre' => $nombre,
                'correo' => $correo,
                'rol' => $rol,
                'activo' => $activo,
            ];

            if ($id > 0) {
                // Actualizar usuario existente
                $datosUsuario['actualizado_en'] = date('Y-m-d H:i:s');

                // Solo actualizar password si se proporcionó uno nuevo
                if (!empty($password)) {
                    $datosUsuario['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
                }

                $this->usuariosRepository->update($id, $datosUsuario);
                return new JsonModel(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
            } else {
                // Crear nuevo usuario
                if (empty($password)) {
                    return new JsonModel(['success' => false, 'message' => 'La contraseña es obligatoria para usuarios nuevos']);
                }

                $datosUsuario['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
                $this->usuariosRepository->create($datosUsuario);
                return new JsonModel(['success' => true, 'message' => 'Usuario creado exitosamente']);
            }
        } catch (\Exception $e) {
            error_log('Error al guardar usuario: ' . $e->getMessage());
            return new JsonModel(['success' => false, 'message' => 'Error al guardar usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener datos de un usuario para edición
     */
    public function obtenerUsuarioAction()
    {
        $id = (int) $this->params()->fromQuery('id', 0);

        if ($id <= 0) {
            return new JsonModel(['success' => false, 'message' => 'ID inválido']);
        }

        $usuario = $this->usuariosRepository->findById($id);

        if (!$usuario) {
            return new JsonModel(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        // No enviar el hash de password
        unset($usuario['password_hash']);

        return new JsonModel(['success' => true, 'usuario' => $usuario]);
    }

    /**
     * Cambiar estado activo/inactivo de un usuario
     */
    public function cambiarEstadoUsuarioAction()
    {
        if (!$this->getRequest()->isPost()) {
            return new JsonModel(['success' => false, 'message' => 'Método no permitido']);
        }

        try {
            $data = $this->getRequest()->getPost();
            $id = (int) ($data['id'] ?? 0);
            $activo = isset($data['activo']) ? (int) $data['activo'] : 0;

            if ($id <= 0) {
                return new JsonModel(['success' => false, 'message' => 'ID inválido']);
            }

            $usuario = $this->usuariosRepository->findById($id);
            if (!$usuario) {
                return new JsonModel(['success' => false, 'message' => 'Usuario no encontrado']);
            }

            // Prevenir desactivar el propio usuario
            $currentUser = $this->authService->getCurrentUser();
            if ((int) $usuario['id'] === (int) $currentUser['id']) {
                return new JsonModel(['success' => false, 'message' => 'No puedes desactivar tu propia cuenta']);
            }

            $this->usuariosRepository->update($id, [
                'activo' => $activo,
                'actualizado_en' => date('Y-m-d H:i:s'),
            ]);

            $mensaje = $activo ? 'Usuario activado exitosamente' : 'Usuario desactivado exitosamente';
            return new JsonModel(['success' => true, 'message' => $mensaje]);
        } catch (\Exception $e) {
            error_log('Error al cambiar estado de usuario: ' . $e->getMessage());
            return new JsonModel(['success' => false, 'message' => 'Error al cambiar estado']);
        }
    }
}
