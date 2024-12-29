<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once './../classes/Database.php';
require_once './../classes/RegistrationService.php';
require_once './../classes/VehicleModelService.php';
require_once './../classes/FuelTypeService.php';
require_once './../classes/VehicleTypeService.php';


$db = (new Database())->getConnection();
$registrationService = new RegistrationService($db);
$vehicleModelService = new VehicleModelService($db);
$vehicleTypeService = new VehicleTypeService($db);
$fuelTypeService = new FuelTypeService($db);



if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $registrationId = $_GET['id'];

    $registration = $registrationService->getRegistrationById($registrationId);

    if ($registration) {
        $currentRegistrationTo = $registration['registration_to'];
        $currentVehicleModelId = $registration['vehicle_model_id'];
        $currentVehicleTypeId = $registration['vehicle_type_id'];
        $currentChassisNumber = $registration['chassis_number'];
        $currentRegistrationNumber = $registration['registration_number'];
        $currentProductionYear = $registration['production_year'];
        $currentFuelTypeId = $registration['fuel_type_id'];

        $currentVehicleModelName = $vehicleModelService->getModelName($currentVehicleModelId);
        $currentVehicleTypeName = $vehicleTypeService->getTypeName($currentVehicleTypeId);
        $currentFuelTypeName = $fuelTypeService->getFuelName($currentFuelTypeId);

    } else {
        echo "Registration not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newVehicleModelName = $_POST['vehicle_model'];
    $newVehicleTypeName = $_POST['vehicle_type'];
    $newFuelTypeName = $_POST['fuel_type'];
    $newChassisNumber = $_POST['chassis_number'];
    $newRegistrationNumber = $_POST['registration_number'];
    $newProductionYear = substr($_POST['production_year'], 0, 4);
    $newRegistrationTo = $_POST['registration_to'];

    $vehicleModelQuery = "SELECT id FROM vehicle_models WHERE name = :name";
    $stmt = $db->prepare($vehicleModelQuery);
    $stmt->execute([':name' => $newVehicleModelName]);
    $vehicleModel = $stmt->fetch(PDO::FETCH_ASSOC);

    $vehicleTypeQuery = "SELECT id FROM vehicle_types WHERE name = :name";
    $stmt = $db->prepare($vehicleTypeQuery);
    $stmt->execute([':name' => $newVehicleTypeName]);
    $vehicleType = $stmt->fetch(PDO::FETCH_ASSOC);

    $fuelTypeQuery = "SELECT id FROM fuel_types WHERE name = :name";
    $stmt = $db->prepare($fuelTypeQuery);
    $stmt->execute([':name' => $newFuelTypeName]);
    $fuelType = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicleModel && $vehicleType && $fuelType) {
        $registrationService->updateRegistration(
            $registrationId,
            $vehicleModel['id'],
            $vehicleType['id'],
            $newChassisNumber,
            $newRegistrationNumber,
            $newProductionYear,
            $fuelType['id'],
            $newRegistrationTo
        );

        header('Location: registration.php');
        exit;
    } else {
        echo "Invalid vehicle model, type, or fuel type.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./../styles/main.css">

</head>

<body>
    <div class="container p-3">
        <h1>Edit Vehicle Registration</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="vehicle_model" class="form-label">Vehicle Model:</label>
                <input list="vehicle_models" name="vehicle_model" id="vehicle_model" class="form-control" required
                    value="<?= htmlspecialchars($currentVehicleModelName) ?>">
                <datalist id="vehicle_models">
                    <?php foreach ($vehicleModels as $model): ?>
                        <option value="<?= htmlspecialchars($model['name']) ?>" <?= $model['name'] == $currentVehicleModelName ? 'selected' : '' ?>>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="mb-3">
                <label for="vehicle_type" class="form-label">Vehicle Type:</label>
                <input list="vehicle_types" name="vehicle_type" id="vehicle_type" class="form-control" required
                    value="<?= htmlspecialchars($currentVehicleTypeName) ?>">
                <datalist id="vehicle_types">
                    <?php foreach ($vehicleTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type['name']) ?>" <?= $type['name'] == $currentVehicleTypeName ? 'selected' : '' ?>>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="mb-3">
                <label for="chassis_number" class="form-label">Vehicle Chassis Number:</label>
                <input type="text" name="chassis_number" id="chassis_number" class="form-control"
                    value="<?= htmlspecialchars($currentChassisNumber) ?>" required>
            </div>

            <div class="mb-3">
                <label for="registration_number" class="form-label">Vehicle Registration Number:</label>
                <input type="text" name="registration_number" id="registration_number" class="form-control"
                    value="<?= htmlspecialchars($currentRegistrationNumber) ?>" required>
            </div>

            <div class="mb-3">
                <label for="production_year" class="form-label">Vehicle Production Year:</label>
                <input type="number" name="production_year" id="production_year" class="form-control"
                    value="<?= htmlspecialchars($currentProductionYear) ?>" required min="1900" max="<?= date('Y') ?>">
            </div>



            <div class="mb-3">
                <label for="fuel_type" class="form-label">Fuel Type:</label>
                <input list="fuel_types" name="fuel_type" id="fuel_type" class="form-control" required
                    value="<?= htmlspecialchars($currentFuelTypeName) ?>">
                <datalist id="fuel_types">
                    <?php foreach ($fuelTypes as $fuel): ?>
                        <option value="<?= htmlspecialchars($fuel['name']) ?>" <?= $fuel['name'] == $currentFuelTypeName ? 'selected' : '' ?>>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="mb-3">
                <label for="registration_to" class="form-label">Registration Expiration Date:</label>
                <input type="date" name="registration_to" id="registration_to" class="form-control"
                    value="<?= htmlspecialchars($currentRegistrationTo) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>