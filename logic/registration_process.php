<?php

session_start();

require_once './../classes/Database.php';
require_once './../classes/VehicleModelService.php';
require_once './../classes/VehicleModel.php';
require_once './../classes/Validator.php';

$db = (new Database())->getConnection();
$vehicleModelService = new VehicleModelService($db);
$validator = new Validator($db);


$vehicleModelName = trim($_POST['vehicle_model']);
$chassisNumber = trim($_POST['chassis_number']);
$registrationNumber = trim($_POST['registration_number']);
$registrationTo = $_POST['registration_to'];
$vehicleType = $_POST['vehicle_type'];
$productionYear = trim($_POST['production_year']);
$productionYear = date("Y", strtotime($productionYear));
$fuelType = $_POST['fuel_type'];


try {
    if (
        !$validator->isNotEmpty($vehicleModelName) || !$validator->isNotEmpty($chassisNumber) ||
        !$validator->isNotEmpty($registrationNumber) || !$validator->isNotEmpty($registrationTo) ||
        !$validator->isNotEmpty($vehicleType) || !$validator->isNotEmpty($productionYear) ||
        !$validator->isNotEmpty($fuelType)
    ) {
        throw new Exception("All fields must be filled.");
    }

    if (!$validator->isChassisNumberUnique($chassisNumber)) {
        throw new Exception("Chassis number already exists in the database.");
    }

    $existingModel = $db->prepare("SELECT id FROM vehicle_models WHERE name = :name");
    $existingModel->execute([':name' => $vehicleModelName]);
    $modelId = $existingModel->fetchColumn();

    if (!$modelId) {
        $insertModel = $db->prepare("INSERT INTO vehicle_models (name) VALUES (:name)");
        $insertModel->execute([':name' => $vehicleModelName]);
        $modelId = $db->lastInsertId();
    }

    $query = 'INSERT INTO registrations (
        vehicle_model_id, chassis_number, registration_number, 
        registration_to, vehicle_type_id, production_year, fuel_type_id
    ) 
    VALUES (
        :vehicle_model_id, :chassis_number, :registration_number, 
        :registration_to, :vehicle_type_id, :production_year, :fuel_type_id
    )';

    $stmt = $db->prepare($query);
    $stmt->execute([
        ':vehicle_model_id' => $modelId,
        ':chassis_number' => $chassisNumber,
        ':registration_number' => $registrationNumber,
        ':registration_to' => $registrationTo,
        ':vehicle_type_id' => $vehicleType,
        ':production_year' => $productionYear,
        ':fuel_type_id' => $fuelType,
    ]);

    $_SESSION['success_message'] = "Vehicle registered successfully!";
    header("Location: ./../public/registration.php");
    exit();
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ./../public/registration.php");
    exit();
}