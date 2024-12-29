<?php

require_once 'VehicleModel.php';

class VehicleModelService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllModels(): array
    {
        $query = 'SELECT id, name FROM vehicle_models';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModelName(int $id): string
    {
        $query = 'SELECT name FROM vehicle_models WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
    }
}
