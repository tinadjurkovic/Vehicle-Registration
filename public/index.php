<?php
session_start();

require_once './../classes/Database.php';
require_once './../classes/VehicleModelService.php';
require_once './../classes/VehicleTypeService.php';
require_once './../classes/FuelTypeService.php';
require_once './../classes/RegistrationService.php';

$db = (new Database())->getConnection();

$vehicleModelService = new VehicleModelService($db);
$vehicleTypeService = new VehicleTypeService($db);
$fuelTypeService = new FuelTypeService($db);
$registrationService = new RegistrationService($db);

$fuelTypes = $fuelTypeService->getAllTypes();
$vehicleTypes = $vehicleTypeService->getAllTypes();
$vehicleModels = $vehicleModelService->getAllModels();

$searchQuery = '';

if (isset($_GET['search_query'])) {
    $searchQuery = $_GET['search_query'];
    $_SESSION['search_query'] = $searchQuery;

    header("Location: index.php");
    exit;
}

if (isset($_SESSION['search_query'])) {
    $searchQuery = $_SESSION['search_query'];
}

if (empty($_GET)) {
    unset($_SESSION['search_query']);
}

$registrations = $registrationService->getAllRegistrations($searchQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./../styles/main.css">
</head>

<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid d-flex flex-row justify-content-space-evenly">
            <a class="navbar-brand" href="index.php">Vehicle Registration</a>
            <a class="text-decoration-none" href="login.php">Login</a>
        </div>
    </nav>

    <div class="container p-5">
        <h1>Welcome</h1>
        <p class="mb-3">Enter your registration number to check its validity</p>
        <form class="d-flex flex-column align-items-center justify-content-center" method="GET" action="index.php">
            <input placeholder="Registration number" name="search_query" type="search" class="form-input p-2 border"
                value="<?= htmlspecialchars($searchQuery); ?>" aria-label="Search">
            <button class="btn btn-primary mt-3" type="submit">Search</button>
        </form>
    </div>

    <?php if (!empty($searchQuery)): ?>
        <?php if (empty($registrations)): ?>
            <div class="alert alert-danger mt-3 text-center" role="alert">
                No such record found.
            </div>
        <?php else: ?>
            <table class="table table-striped mt-3 border">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Vehicle Model</th>
                        <th scope="col">Vehicle Type</th>
                        <th scope="col">Vehicle Chassis Number</th>
                        <th scope="col">Vehicle Production Year</th>
                        <th scope="col">Registration Number</th>
                        <th scope="col">Fuel Type</th>
                        <th scope="col">Registration to</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td><?= htmlspecialchars($registration['id']); ?></td>
                            <td>
                                <?= htmlspecialchars(array_column($vehicleModels, 'name', 'id')[$registration['vehicle_model_id']] ?? ''); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(array_column($vehicleTypes, 'name', 'id')[$registration['vehicle_type_id']] ?? ''); ?>
                            </td>
                            <td><?= htmlspecialchars($registration['chassis_number']); ?></td>
                            <td><?= htmlspecialchars($registration['production_year']); ?></td>
                            <td><?= htmlspecialchars($registration['registration_number']); ?></td>
                            <td>
                                <?= htmlspecialchars(array_column($fuelTypes, 'name', 'id')[$registration['fuel_type_id']] ?? ''); ?>
                            </td>
                            <td><?= htmlspecialchars($registration['registration_to']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>