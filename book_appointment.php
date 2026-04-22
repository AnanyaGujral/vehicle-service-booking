<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id      = $_SESSION['user_id'];
    $vehicle_id   = intval($_POST['vehicle_id']);
    $slot_id      = intval($_POST['slot_id']);
    $service_type = mysqli_real_escape_string($conn, trim($_POST['service_type']));
    $description  = mysqli_real_escape_string($conn, trim($_POST['description']));

    // Check slot availability
    $slotCheck = mysqli_query($conn, "SELECT * FROM service_slots WHERE id='$slot_id' AND is_available=1");
    if (mysqli_num_rows($slotCheck) == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Selected slot is no longer available.']);
        exit();
    }

    // Check if user already has a booking for same slot
    $dupCheck = mysqli_query($conn, "SELECT id FROM appointments WHERE user_id='$user_id' AND slot_id='$slot_id' AND status != 'Cancelled'");
    if (mysqli_num_rows($dupCheck) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You already have a booking for this slot.']);
        exit();
    }

    // Insert appointment
    $sql = "INSERT INTO appointments (user_id, vehicle_id, slot_id, service_type, description)
            VALUES ('$user_id', '$vehicle_id', '$slot_id', '$service_type', '$description')";

    if (mysqli_query($conn, $sql)) {
        // Mark slot as unavailable
        mysqli_query($conn, "UPDATE service_slots SET is_available=0 WHERE id='$slot_id'");
        echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking failed. Please try again.']);
    }
}
?>
