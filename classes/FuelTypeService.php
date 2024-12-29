<?php
class FuelTypeService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllTypes(): array
    {
        $query = 'SELECT id, name FROM fuel_types';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFuelName(int $id): string
    {
        $query = 'SELECT name FROM fuel_types WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetchColumn();
        return $result;
    }

}