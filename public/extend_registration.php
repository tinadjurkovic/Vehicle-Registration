<?php
session_start();

require_once './../classes/Database.php';
require_once './../classes/RegistrationService.php';

$database = new Database();
$db = $database->getConnection();

$registrationService = new RegistrationService($db);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $registrationId = $_GET['id'];

    $registration = $registrationService->getRegistrationById($registrationId);

    if ($registration) {
        $currentRegistrationTo = $registration['registration_to'];
    } else {
        echo "Registration not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRegistrationTo = $_POST['registration_to'];

    $registrationService->extendRegistration($registrationId, $newRegistrationTo);

    header('Location: registration.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extend Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./../styles/main.css">
</head>

<body>
    <div class="container p-3">
        <h1>Extend Registration</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="registration_to" class="form-label">New Registration Expiration Date:</label>
                <input type="date" name="registration_to" id="registration_to" class="form-control"
                    value="<?= htmlspecialchars($currentRegistrationTo) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>