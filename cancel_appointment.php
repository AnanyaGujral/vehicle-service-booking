<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id        = $_SESSION['user_id'];
    $appointment_id = intval($_POST['appointment_id']);

    // Get slot_id for this appointment
    $res  = mysqli_query($conn, "SELECT slot_id FROM appointments WHERE id='$appointment_id' AND user_id='$user_id'");
    $row  = mysqli_fetch_assoc($res);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Appointment not found.']);
        exit();
    }

    $slot_id = $row['slot_id'];

    // Cancel appointment
    mysqli_query($conn, "UPDATE appointments SET status='Cancelled' WHERE id='$appointment_id' AND user_id='$user_id'");

    // Re-open the slot
    mysqli_query($conn, "UPDATE service_slots SET is_available=1 WHERE id='$slot_id'");

    echo json_encode(['status' => 'success', 'message' => 'Appointment cancelled successfully.']);
}
?>
