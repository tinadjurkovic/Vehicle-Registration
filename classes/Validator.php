<?php

require_once 'Database.php';

class Validator
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function isChassisNumberUnique($chassisNumber)
    {
        $query = "SELECT COUNT(*) FROM registrations WHERE chassis_number = :chassis_number";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':chassis_number' => $chassisNumber]);

        return $stmt->fetchColumn() == 0;
    }

    public function isNotEmpty($field)
    {
        return !empty(trim($field));
    }

    public function isValidSearchParam($param): bool
    {
        $validParams = ['vehicle_model', 'registration_number', 'chassis_number'];
        return in_array($param, $validParams);
    }

    public function isValidSearchValue($value): bool
    {
        return !empty($value) && is_string($value);
    }
}
