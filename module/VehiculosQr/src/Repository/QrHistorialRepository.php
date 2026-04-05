<?php

namespace VehiculosQr\Repository;

use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class QrHistorialRepository
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function findByRegistroId(int $registroId, int $limit = 50): array
    {
        $select = new Select($this->tableGateway->getTable());
        $select->where(['qr_registro_id' => $registroId]);
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

    public function deleteByRegistroId(int $registroId): bool
    {
        return (bool) $this->tableGateway->delete(['qr_registro_id' => $registroId]);
    }
}
