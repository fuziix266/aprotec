<?php

namespace VehiculosQr\Repository;

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
}
