<?php

namespace VehiculosQr\Service;

use VehiculosQr\Repository\QrCodigosRepository;
use VehiculosQr\Repository\QrRegistrosRepository;
use VehiculosQr\Repository\QrHistorialRepository;

class QrService
{
    private QrCodigosRepository $qrCodigosRepo;
    private QrRegistrosRepository $qrRegistrosRepo;
    private QrHistorialRepository $qrHistorialRepo;

    public function __construct(
        QrCodigosRepository $qrCodigosRepo,
        QrRegistrosRepository $qrRegistrosRepo,
        QrHistorialRepository $qrHistorialRepo
    ) {
        $this->qrCodigosRepo = $qrCodigosRepo;
        $this->qrRegistrosRepo = $qrRegistrosRepo;
        $this->qrHistorialRepo = $qrHistorialRepo;
    }

    /**
     * Buscar QR por UUID
     */
    public function buscarPorUuid(string $uuid): ?array
    {
        return $this->qrCodigosRepo->findByUuid($uuid);
    }

    /**
     * Buscar QR por ID
     */
    public function buscarPorId(int $id): ?array
    {
        return $this->qrCodigosRepo->findById($id);
    }

    /**
     * Obtener registro asociado al QR
     */
    public function obtenerRegistroPorQrId(int $qrId): ?array
    {
        return $this->qrRegistrosRepo->findByQrCodigoId($qrId);
    }

    /**
     * Crear código QR
     */
    public function crearCodigoQr(string $observaciones = null): array
    {
        $uuid = $this->qrCodigosRepo->generateUuid();
        
        $data = [
            'uuid_qr' => $uuid,
            'estado' => 'PENDIENTE',
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones,
        ];
        
        $id = $this->qrCodigosRepo->create($data);
        $data['id'] = $id;
        
        return $data;
    }

    /**
     * Generar lote de códigos QR
     */
    public function generarLoteQr(int $cantidad): array
    {
        $codigos = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $codigos[] = $this->crearCodigoQr("Lote generado - " . date('Y-m-d H:i:s'));
        }
        return $codigos;
    }

    /**
     * Generar código de confirmación de 6 dígitos
     */
    public function generarCodigoConfirmacion(): string
    {
        return str_pad((string) mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Crear o actualizar registro inicial con correo
     */
    public function crearRegistroInicial(int $qrCodigoId, string $correo, string $ip): array
    {
        $codigo = $this->generarCodigoConfirmacion();
        $expira = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        $existente = $this->qrRegistrosRepo->findByQrCodigoId($qrCodigoId);
        
        $data = [
            'correo_funcionario' => $correo,
            'codigo_confirmacion' => $codigo,
            'codigo_confirmacion_expira' => $expira,
            'correo_confirmado' => 0,
            'creado_por_ip' => $ip,
        ];
        
        if ($existente) {
            // Actualizar
            $this->qrRegistrosRepo->updateByQrCodigoId($qrCodigoId, $data);
            $registroId = $existente['id'];
        } else {
            // Crear nuevo
            $data['qr_codigo_id'] = $qrCodigoId;
            $registroId = $this->qrRegistrosRepo->create($data);
        }
        
        $data['id'] = $registroId;
        return $data;
    }

    /**
     * Confirmar código de verificación
     */
    public function confirmarCodigo(int $qrCodigoId, string $codigo): array
    {
        $registro = $this->qrRegistrosRepo->findByQrCodigoId($qrCodigoId);
        
        if (!$registro) {
            return ['success' => false, 'error' => 'Registro no encontrado'];
        }
        
        if ($registro['correo_confirmado'] == 1) {
            return ['success' => false, 'error' => 'El correo ya fue confirmado'];
        }
        
        if ($registro['codigo_confirmacion'] !== $codigo) {
            return ['success' => false, 'error' => 'Código incorrecto'];
        }
        
        $expira = strtotime($registro['codigo_confirmacion_expira']);
        if (time() > $expira) {
            return ['success' => false, 'error' => 'El código ha expirado'];
        }
        
        // Confirmar
        $this->qrRegistrosRepo->updateByQrCodigoId($qrCodigoId, [
            'correo_confirmado' => 1,
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
        ]);
        
        // Cambiar estado del QR a ASIGNADO
        $this->qrCodigosRepo->update($qrCodigoId, [
            'estado' => 'ASIGNADO',
            'fecha_asignacion' => date('Y-m-d H:i:s'),
        ]);
        
        return ['success' => true];
    }

    /**
     * Guardar datos del funcionario y vehículo
     */
    public function guardarDatos(int $qrCodigoId, array $datos, string $ip, string $correoQuien): bool
    {
        $registro = $this->qrRegistrosRepo->findByQrCodigoId($qrCodigoId);
        
        if (!$registro) {
            return false;
        }
        
        $datosAnteriores = $registro;
        $esPrimeraVez = empty($registro['fecha_registro']);
        
        $datosActualizar = [
            'nombres' => $datos['nombres'] ?? '',
            'apellidos' => $datos['apellidos'] ?? '',
            'rut' => $datos['rut'] ?? null,
            'unidad' => $datos['unidad'] ?? null,
            'cargo' => $datos['cargo'] ?? null,
            'celular' => $datos['celular'] ?? '',
            'anexo' => $datos['anexo'] ?? null,
            'patente' => $datos['patente'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null,
            'actualizado_por_ip' => $ip,
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
        ];
        
        if ($esPrimeraVez) {
            $datosActualizar['fecha_registro'] = date('Y-m-d H:i:s');
        }
        
        $this->qrRegistrosRepo->updateByQrCodigoId($qrCodigoId, $datosActualizar);
        
        // Registrar en historial
        $cambios = $this->calcularCambios($datosAnteriores, $datosActualizar);
        $this->qrHistorialRepo->create([
            'qr_registro_id' => $registro['id'],
            'quien_correo' => $correoQuien,
            'accion' => $esPrimeraVez ? 'CREAR' : 'EDITAR',
            'cambios_json' => json_encode($cambios),
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'fecha_evento' => date('Y-m-d H:i:s'),
        ]);
        
        return true;
    }

    /**
     * Calcular diferencias entre datos anteriores y nuevos
     */
    private function calcularCambios(array $anterior, array $nuevo): array
    {
        $cambios = [];
        $campos = ['nombres', 'apellidos', 'rut', 'unidad', 'cargo', 'celular', 'anexo', 'patente', 'observaciones'];
        
        foreach ($campos as $campo) {
            $valorAnterior = $anterior[$campo] ?? '';
            $valorNuevo = $nuevo[$campo] ?? '';
            
            if ($valorAnterior != $valorNuevo) {
                $cambios[$campo] = [$valorAnterior, $valorNuevo];
            }
        }
        
        return $cambios;
    }

    /**
     * Obtener datos completos (QR + Registro + Historial)
     */
    public function obtenerDatosCompletos(string $uuid): ?array
    {
        $qr = $this->buscarPorUuid($uuid);
        if (!$qr) {
            return null;
        }
        
        $registro = $this->obtenerRegistroPorQrId($qr['id']);
        $historial = $registro ? $this->qrHistorialRepo->findByRegistroId($registro['id'], 10) : [];
        
        return [
            'qr' => $qr,
            'registro' => $registro,
            'historial' => $historial,
        ];
    }

    /**
     * Cambiar estado del QR
     * Estados válidos: PENDIENTE, HABILITADO, DESHABILITADO
     */
    public function cambiarEstado(int $qrId, string $nuevoEstado): bool
    {
        $estadosValidos = ['PENDIENTE', 'HABILITADO', 'DESHABILITADO'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            error_log("QrService::cambiarEstado - Estado inválido: {$nuevoEstado}");
            return false;
        }
        
        $resultado = $this->qrCodigosRepo->update($qrId, ['estado' => $nuevoEstado]);
        
        if ($resultado) {
            error_log("QrService::cambiarEstado - QR ID {$qrId} cambió a estado {$nuevoEstado}");
        }
        
        return $resultado;
    }

    /**
     * Obtener registro por correo
     */
    public function obtenerRegistroPorCorreo(string $correo): ?array
    {
        return $this->qrRegistrosRepo->findByCorreo($correo);
    }

    /**
     * Crear registro completo (usado por admin)
     */
    public function crearRegistro(int $qrCodigoId, array $datos): bool
    {
        try {
            $registroId = $this->qrRegistrosRepo->create($datos);
            
            if ($registroId) {
                // Actualizar fecha de asignación en qr_codigos
                $this->qrCodigosRepo->update($qrCodigoId, [
                    'fecha_asignacion' => date('Y-m-d H:i:s')
                ]);
                
                // Registrar en historial
                $usuario = $_SESSION['usuario_admin'] ?? 'admin';
                $this->qrHistorialRepo->create([
                    'qr_registro_id' => $registroId,
                    'quien_correo' => $usuario,
                    'accion' => 'CREAR',
                    'cambios_json' => json_encode(['mensaje' => 'Registro creado por administrador']),
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'fecha_evento' => date('Y-m-d H:i:s'),
                ]);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error al crear registro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar datos del registro (usado por admin)
     */
    public function actualizarDatos(int $qrCodigoId, array $datos): bool
    {
        try {
            $registro = $this->qrRegistrosRepo->findByQrCodigoId($qrCodigoId);
            
            if (!$registro) {
                return false;
            }
            
            $datosAnteriores = $registro;
            
            // Actualizar el registro
            $resultado = $this->qrRegistrosRepo->updateByQrCodigoId($qrCodigoId, $datos);
            
            if ($resultado) {
                // Registrar en historial
                $cambios = $this->calcularCambios($datosAnteriores, $datos);
                $usuario = $_SESSION['usuario_admin'] ?? 'admin';
                
                $this->qrHistorialRepo->create([
                    'qr_registro_id' => $registro['id'],
                    'quien_correo' => $usuario,
                    'accion' => 'EDITAR',
                    'cambios_json' => json_encode($cambios),
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'fecha_evento' => date('Y-m-d H:i:s'),
                ]);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error al actualizar datos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener registro por ID
     */
    public function obtenerRegistroPorId(int $id): ?array
    {
        return $this->qrRegistrosRepo->findById($id);
    }

    /**
     * Actualizar código de edición
     */
    public function actualizarCodigoEdicion(int $registroId, string $codigo, string $expira): bool
    {
        return $this->qrRegistrosRepo->update($registroId, [
            'codigo_confirmacion' => $codigo,
            'codigo_confirmacion_expira' => $expira,
        ]);
    }

    /**
     * Obtener QR por registro ID
     */
    public function obtenerQrPorRegistroId(int $registroId): ?array
    {
        $registro = $this->qrRegistrosRepo->findById($registroId);
        if (!$registro) {
            return null;
        }
        return $this->qrCodigosRepo->findById($registro['qr_codigo_id']);
    }

    /**
     * Listar todos los códigos QR con paginación
     */
    public function listarQrPaginado(int $page = 1, int $limit = 20): array
    {
        return $this->qrCodigosRepo->findAllPaginated($page, $limit);
    }

    /**
     * Contar total de QR
     */
    public function contarQr(): int
    {
        return $this->qrCodigosRepo->count();
    }
}
