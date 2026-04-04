<?php

namespace VehiculosQr\Service;

use VehiculosQr\Repository\QrHistorialRepository;

class QrHistorialService
{
    private QrHistorialRepository $historialRepo;

    public function __construct(QrHistorialRepository $historialRepo)
    {
        $this->historialRepo = $historialRepo;
    }

    /**
     * Registrar cambio en el historial
     */
    public function registrarCambio(
        int $qrRegistroId,
        string $quienCorreo,
        string $accion,
        array $cambios = [],
        ?string $ip = null,
        ?string $userAgent = null
    ): int {
        $data = [
            'qr_registro_id' => $qrRegistroId,
            'quien_correo' => $quienCorreo,
            'accion' => $accion,
            'cambios_json' => json_encode($cambios),
            'ip' => $ip ?? $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
            'fecha_evento' => date('Y-m-d H:i:s'),
        ];
        
        return $this->historialRepo->create($data);
    }

    /**
     * Obtener historial de un registro
     */
    public function obtenerHistorialPorRegistro(int $qrRegistroId, int $limit = 50): array
    {
        return $this->historialRepo->findByRegistroId($qrRegistroId, $limit);
    }
}
