<?php
class RegistrationService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllRegistrations($searchQuery = '')
    {
        $query = "SELECT * FROM registrations";

        if (!empty($searchQuery)) {
            $query .= " WHERE registration_number LIKE :search";
        }

        $stmt = $this->db->prepare($query);

        if (!empty($searchQuery)) {
            $stmt->bindParam(':search', $searchQuery);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleData()
    {
        $fuelTypes = $this->db->query("SELECT id, name FROM fuel_types")->fetchAll(PDO::FETCH_ASSOC);
        $vehicleTypes = $this->db->query("SELECT id, name FROM vehicle_types")->fetchAll(PDO::FETCH_ASSOC);
        $vehicleModels = $this->db->query("SELECT id, name FROM vehicle_models")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'fuelTypes' => $fuelTypes,
            'vehicleTypes' => $vehicleTypes,
            'vehicleModels' => $vehicleModels
        ];
    }
    public function searchRegistrations($searchQuery)
    {
        $query = "SELECT * FROM registrations";
        if (!empty($searchQuery)) {
            $query .= " WHERE 
            vehicle_model_id IN (SELECT id FROM vehicle_models WHERE name LIKE :search) OR 
            registration_number LIKE :search OR 
            chassis_number LIKE :search";
        }

        $stmt = $this->db->prepare($query);
        if (!empty($searchQuery)) {
            $stmt->bindValue(':search', '%' . $searchQuery . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getExpirationColor($registrationTo)
    {
        $currentDate = date('Y-m-d');
        $expirationDate = date('Y-m-d', strtotime($registrationTo));
        $daysToExpire = floor((strtotime($registrationTo) - strtotime($currentDate)) / (60 * 60 * 24));

        if ($daysToExpire < 0) {
            return 'red';
        } elseif ($daysToExpire <= 30) {
            return '#CCCC00';
        }

        return '';
    }

    public function getRegistrationById($registrationId)
    {
        $query = "SELECT * FROM registrations WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $registrationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function extendRegistration($registrationId, $newRegistrationTo)
    {
        $updateQuery = "UPDATE registrations SET registration_to = :registration_to WHERE id = :id";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->execute([
            ':registration_to' => $newRegistrationTo,
            ':id' => $registrationId
        ]);
    }

    public function updateRegistration($registrationId, $vehicleModelId, $vehicleTypeId, $chassisNumber, $registrationNumber, $productionYear, $fuelTypeId, $registrationTo)
    {
        $updateQuery = "UPDATE registrations SET 
                        vehicle_model_id = :vehicle_model,
                        vehicle_type_id = :vehicle_type,
                        chassis_number = :chassis_number,
                        registration_number = :registration_number,
                        production_year = :production_year,
                        fuel_type_id = :fuel_type,
                        registration_to = :registration_to 
                        WHERE id = :id";

        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->execute([
            ':vehicle_model' => $vehicleModelId,
            ':vehicle_type' => $vehicleTypeId,
            ':chassis_number' => $chassisNumber,
            ':registration_number' => $registrationNumber,
            ':production_year' => $productionYear,
            ':fuel_type' => $fuelTypeId,
            ':registration_to' => $registrationTo,
            ':id' => $registrationId
        ]);
    }
}