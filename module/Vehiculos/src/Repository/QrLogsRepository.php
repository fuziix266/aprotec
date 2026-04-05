<?php

namespace Vehiculos\Repository;

use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class QrLogsRepository
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function findByQrCodigoId(int $qrCodigoId, int $limit = 100): array
    {
        $select = new Select($this->tableGateway->getTable());
        $select->where(['qr_codigo_id' => $qrCodigoId]);
        $select->order('fecha_evento DESC');
        $select->limit($limit);

        $rowset = $this->tableGateway->selectWith($select);
        return iterator_to_array($rowset);
    }

    public function create(array $data): int
    {
        $this->tableGateway->insert($data);
        return (int) $this->tableGateway->getLastInsertValue();
    }

    public function findEscaneosSospechosos(int $qrCodigoId): array
    {
        $sql = "SELECT * FROM qr_logs 
                WHERE qr_codigo_id = ? 
                AND (TIME(fecha_evento) < '06:00:00' OR TIME(fecha_evento) > '22:00:00')
                ORDER BY fecha_evento DESC
                LIMIT 50";

        $adapter = $this->tableGateway->getAdapter();
        $statement = $adapter->createStatement($sql, [$qrCodigoId]);
        $result = $statement->execute();

        $logs = [];
        foreach ($result as $row) {
            $logs[] = (array) $row;
        }

        return $logs;
    }

    /**
     * Obtener logs globales
     */
    public function findGlobal(int $limit = 200): array
    {
        $sql = "SELECT l.*, q.uuid_qr as qr_uuid, u.correo as usuario_correo
                FROM qr_logs l
                LEFT JOIN qr_codigos q ON l.qr_codigo_id = q.id
                LEFT JOIN qr_usuarios u ON l.usuario_id = u.id
                ORDER BY l.fecha_evento DESC
                LIMIT ?";

        $adapter = $this->tableGateway->getAdapter();
        $statement = $adapter->createStatement($sql, [$limit]);
        $result = $statement->execute();

        $logs = [];
        foreach ($result as $row) {
            $logs[] = (array) $row;
        }

        return $logs;
    }

    /**
     * Obtener escaneos sospechosos globales
     */
    public function findEscaneosSospechososGlobal(int $limit = 50): array
    {
        $sql = "SELECT l.*, q.uuid_qr as qr_uuid, u.correo as usuario_correo
                FROM qr_logs l
                LEFT JOIN qr_codigos q ON l.qr_codigo_id = q.id
                LEFT JOIN qr_usuarios u ON l.usuario_id = u.id
                WHERE (TIME(l.fecha_evento) < '06:00:00' OR TIME(l.fecha_evento) > '22:00:00')
                ORDER BY l.fecha_evento DESC
                LIMIT ?";

        $adapter = $this->tableGateway->getAdapter();
        $statement = $adapter->createStatement($sql, [$limit]);
        $result = $statement->execute();

        $logs = [];
        foreach ($result as $row) {
            $logs[] = (array) $row;
        }

        return $logs;
    }
}
