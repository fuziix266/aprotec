<?php

namespace VehiculosQr\Repository;

use Laminas\Db\TableGateway\TableGatewayInterface;

class QrRegistrosRepository
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function findById(int $id): ?array
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        return $row ? (array) $row : null;
    }

    public function findByQrCodigoId(int $qrCodigoId): ?array
    {
        $rowset = $this->tableGateway->select(['qr_codigo_id' => $qrCodigoId]);
        $row = $rowset->current();
        return $row ? (array) $row : null;
    }

    public function findByCorreo(string $correo): ?array
    {
        $rowset = $this->tableGateway->select(['correo_funcionario' => $correo]);
        $row = $rowset->current();
        return $row ? (array) $row : null;
    }

    public function create(array $data): int
    {
        $this->tableGateway->insert($data);
        return (int) $this->tableGateway->getLastInsertValue();
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->tableGateway->update($data, ['id' => $id]);
    }

    public function updateByQrCodigoId(int $qrCodigoId, array $data): bool
    {
        return (bool) $this->tableGateway->update($data, ['qr_codigo_id' => $qrCodigoId]);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->tableGateway->delete(['id' => $id]);
    }
}
