<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $new_status     = mysqli_real_escape_string($conn, trim($_POST['status']));

    mysqli_query($conn, "UPDATE appointments SET status='$new_status' WHERE id='$appointment_id'");

    // If marked Completed, log to service_history
    if ($new_status == 'Completed') {
        $appt = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT a.*, s.slot_date FROM appointments a
             JOIN service_slots s ON a.slot_id = s.id
             WHERE a.id='$appointment_id'"));

        $cost    = mysqli_real_escape_string($conn, trim($_POST['cost'] ?? '0'));
        $remarks = mysqli_real_escape_string($conn, trim($_POST['remarks'] ?? ''));

        $sql = "INSERT INTO service_history (appointment_id, vehicle_id, user_id, service_type, service_date, cost, remarks)
                VALUES ('{$appt['id']}', '{$appt['vehicle_id']}', '{$appt['user_id']}',
                        '{$appt['service_type']}', '{$appt['slot_date']}', '$cost', '$remarks')";
        mysqli_query($conn, $sql);
    }

    echo json_encode(['status' => 'success', 'message' => 'Status updated.']);
}
?>
