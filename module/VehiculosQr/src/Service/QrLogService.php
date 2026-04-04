<?php

namespace VehiculosQr\Service;

use VehiculosQr\Repository\QrLogsRepository;

class QrLogService
{
    private QrLogsRepository $logsRepo;

    public function __construct(QrLogsRepository $logsRepo)
    {
        $this->logsRepo = $logsRepo;
    }

    /**
     * Registrar escaneo/evento
     */
    public function registrarEvento(
        int $qrCodigoId,
        string $tipo,
        ?array $gps = null,
        ?int $usuarioId = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): int {
        $data = [
            'qr_codigo_id' => $qrCodigoId,
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'ip' => $ip ?? $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
            'lat' => $gps['lat'] ?? null,
            'lon' => $gps['lon'] ?? null,
            'gps_accuracy_m' => $gps['accuracy'] ?? null,
            'fecha_evento' => date('Y-m-d H:i:s'),
        ];
        
        return $this->logsRepo->create($data);
    }

    /**
     * Obtener logs de un QR
     */
    public function obtenerLogsPorQr(int $qrCodigoId, int $limit = 100): array
    {
        return $this->logsRepo->findByQrCodigoId($qrCodigoId, $limit);
    }

    /**
     * Obtener escaneos sospechosos
     */
    public function obtenerEscaneosSospechosos(int $qrCodigoId): array
    {
        return $this->logsRepo->findEscaneosSospechosos($qrCodigoId);
    }
}
