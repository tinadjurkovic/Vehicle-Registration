<?php
session_start();

$message = ['error' => '', 'success' => ''];

if (isset($_SESSION['username'])) {
    header("Location: registration.php");
    die();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once './../classes/Database.php';
    require_once './../classes/AdminService.php';

    $db = (new Database())->getConnection();
    $adminService = new AdminService($db);

    $admin = $adminService->authenticate($_POST['username'], $_POST['password']);

    if ($admin) {
        $_SESSION['username'] = $admin['username'];
        $message['success'] = "Login successful!";
        header("Location: registration.php");
        die();
    } else {
        $message['error'] = "Wrong credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./../styles/main.css">
</head>

<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid d-flex flex-row justify-content-space-evenly">
            <a class="text-decoration-none" href="index.php">Back to Homepage</a>
        </div>
    </nav>
    <?php
    if (!empty($message['error'])) {
        echo "<div class='row'><div class='col-12'><div class='alert alert-danger'>{$message['error']}</div></div></div>";
    } else if (!empty($message['success'])) {
        echo "<div class='row'><div class='col-12'><div class='alert alert-success'>{$message['success']}</div></div></div>";
    }
    ?>
    <div class="container p-5">
        <h4 class="mb-4">Enter your ADMIN credentials:</h4>
        <form method="POST" class="d-flex flex-column justify-content-center">
            <label for="username">Username: </label>
            <input class="mb-4" id="username" type="text" name="username">
            <label for="password">Password: </label>
            <input class="mb-4" id="password" type="password" name="password">
            <button type="submit" class="btn btn-primary"> Login</button>
        </form>
    </div>
</body>

</html>