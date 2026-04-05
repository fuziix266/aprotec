<?php

namespace Vehiculos\Repository;

use Laminas\Db\TableGateway\TableGatewayInterface;

class QrUsuariosRepository
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

    public function findByCorreo(string $correo): ?array
    {
        $rowset = $this->tableGateway->select(['correo' => $correo]);
        $row = $rowset->current();
        return $row ? (array) $row : null;
    }

    public function findAll(): array
    {
        $rowset = $this->tableGateway->select();
        return iterator_to_array($rowset);
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

    public function delete(int $id): bool
    {
        return (bool) $this->tableGateway->delete(['id' => $id]);
    }
}
