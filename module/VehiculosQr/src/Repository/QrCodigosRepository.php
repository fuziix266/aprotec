<?php

namespace VehiculosQr\Repository;

use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\Sql\Select;

class QrCodigosRepository
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

    public function findByUuid(string $uuid): ?array
    {
        $rowset = $this->tableGateway->select(['uuid_qr' => $uuid]);
        $row = $rowset->current();
        return $row ? (array) $row : null;
    }

    public function findAll(): array
    {
        $rowset = $this->tableGateway->select();
        return iterator_to_array($rowset);
    }

    public function findAllPaginated(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        
        $select = new Select($this->tableGateway->getTable());
        $select->limit($limit)->offset($offset);
        $select->order('id DESC');
        
        $rowset = $this->tableGateway->selectWith($select);
        return iterator_to_array($rowset);
    }

    public function count(): int
    {
        $rowset = $this->tableGateway->select();
        return $rowset->count();
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

    public function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
