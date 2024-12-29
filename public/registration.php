<?php
session_start();

require_once './../classes/Database.php';
require_once './../classes/RegistrationService.php';

$database = new Database();
$db = $database->getConnection();

$registrationService = new RegistrationService($db);

$vehicleData = $registrationService->getVehicleData();
$fuelTypes = $vehicleData['fuelTypes'];
$vehicleTypes = $vehicleData['vehicleTypes'];
$vehicleModels = $vehicleData['vehicleModels'];

$searchQuery = '';

if (isset($_GET['search_query'])) {
    $searchQuery = $_GET['search_query'];
    $_SESSION['search_query'] = $searchQuery;

    header("Location: registration.php");
    exit;
}

if (isset($_SESSION['search_query'])) {
    $searchQuery = $_SESSION['search_query'];
}

if (empty($_GET)) {
    unset($_SESSION['search_query']);
}

$registrations = $registrationService->searchRegistrations($searchQuery);

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
            <a class="text-decoration-none" href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container p-3">
        <h1>Vehicle Registration</h1>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<p style="color: green;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
            unset($_SESSION['success_message']);


        }

        if (isset($_SESSION['error_message'])) {
            echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
            unset($_SESSION['error_message']);

        }
        ?>
        <form method="POST" action="./../logic/registration_process.php">
            <div class="form-box d-flex">
                <div class="d-flex flex-column wrapper me-5 justify-content-center">
                    <label for="browser">Vehicle Model:</label>
                    <input list="browsers" name="vehicle_model" id="browser" class="form-input mb-3">
                    <datalist id="browsers">
                        <?php foreach ($vehicleModels as $model): ?>
                            <option value="<?= htmlspecialchars($model['name']) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                    <label for="chassis_number">Vehicle chassis number:</label>
                    <input id="chassis_number" name="chassis_number" type="text" class="form-input mb-3">
                    <label for="registration_number">Vehicle registration number:</label>
                    <input id="registration_number" name="registration_number" type="text" class="form-input mb-3">
                    <label for="registration_to">Registration to:</label>
                    <input id="registration_to" name="registration_to" type="date" class="form-input mb-3">
                </div>
                <div class="wrapper d-flex flex-column">
                    <label for="vehicle_type">Vehicle Type:</label>
                    <select name="vehicle_type" id="vehicle_type" class="form-input mb-4" required>
                        <option selected disabled>Default select</option>
                        <?php foreach ($vehicleTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="production_year">Vehicle production year:</label>
                    <input id="production_year" name="production_year" type="date" class="form-input mb-4">
                    <label for="fuel_type">Fuel Type:</label>
                    <select name="fuel_type" id="fuel_type" class="form-input mb-4" required>
                        <option selected disabled>Default select</option>
                        <?php foreach ($fuelTypes as $fuel): ?>
                            <option value="<?= htmlspecialchars($fuel['id']) ?>"><?= htmlspecialchars($fuel['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary mt-3">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <div class="navbar bg-body-tertiary mt-3">
        <div class="container-fluid">
            <form class="d-flex" method="GET" action="registration.php">
                <input class="form-control me-2" type="search" name="search_query" placeholder="Search..."
                    value="<?= htmlspecialchars($searchQuery); ?>" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="container-fluid">
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
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $registration): ?>
                    <?php
                    $registrationTo = $registration['registration_to'];
                    $textColor = $registrationService->getExpirationColor($registrationTo);

                    $vehicleModelName = '';
                    foreach ($vehicleModels as $vehicleModel) {
                        if ($vehicleModel['id'] == $registration['vehicle_model_id']) {
                            $vehicleModelName = $vehicleModel['name'];
                            break;
                        }
                    }

                    $vehicleTypeName = '';
                    foreach ($vehicleTypes as $vehicleType) {
                        if ($vehicleType['id'] == $registration['vehicle_type_id']) {
                            $vehicleTypeName = $vehicleType['name'];
                            break;
                        }
                    }

                    $fuelTypeName = '';
                    foreach ($fuelTypes as $fuelType) {
                        if ($fuelType['id'] == $registration['fuel_type_id']) {
                            $fuelTypeName = $fuelType['name'];
                            break;
                        }
                    }
                    ?>
                    <tr>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($registration['id']); ?>
                        </td>

                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($vehicleModelName); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($vehicleTypeName); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($registration['chassis_number']); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($registration['production_year']); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($registration['registration_number']); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($fuelTypeName); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <?= htmlspecialchars($registration['registration_to']); ?>
                        </td>
                        <td style="color: <?= htmlspecialchars($textColor); ?>">
                            <a href="./../logic/delete_registration.php?id=<?= $registration['id']; ?>"
                                class="btn btn-danger p-1">Delete</a>
                            <a href="edit_registration.php?id=<?= $registration['id']; ?>"
                                class="btn btn-warning p-1">Edit</a>
                            <?php
                            if ($textColor == 'red' || $textColor == '#CCCC00'):
                                ?>
                                <a href="extend_registration.php?id=<?= $registration['id']; ?>"
                                    class="btn btn-success p-1">Extend</a>
                                <?php
                            endif ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>