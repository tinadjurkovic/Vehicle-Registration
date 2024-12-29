<?php
session_start();

require_once './../classes/Database.php';

$db = (new Database())->getConnection();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $registrationId = $_GET['id'];

    try {
        $query = "DELETE FROM registrations WHERE id = :id";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':id', $registrationId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: ./../public/registration.php');
            exit;
        } else {
            echo "Error deleting registration.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}