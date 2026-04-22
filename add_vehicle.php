<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id        = $_SESSION['user_id'];
    $vehicle_name   = mysqli_real_escape_string($conn, trim($_POST['vehicle_name']));
    $vehicle_number = mysqli_real_escape_string($conn, trim($_POST['vehicle_number']));
    $vehicle_type   = mysqli_real_escape_string($conn, trim($_POST['vehicle_type']));
    $brand          = mysqli_real_escape_string($conn, trim($_POST['brand']));
    $model          = mysqli_real_escape_string($conn, trim($_POST['model']));
    $year           = intval($_POST['year']);

    $check = mysqli_query($conn, "SELECT id FROM vehicles WHERE vehicle_number='$vehicle_number'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle number already registered.']);
    } else {
        $sql = "INSERT INTO vehicles (user_id, vehicle_name, vehicle_number, vehicle_type, brand, model, year)
                VALUES ('$user_id', '$vehicle_name', '$vehicle_number', '$vehicle_type', '$brand', '$model', '$year')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Vehicle added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add vehicle.']);
        }
    }
}
?>
