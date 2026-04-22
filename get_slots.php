<?php
require 'config.php';

$sql    = "SELECT * FROM service_slots WHERE is_available=1 AND slot_date >= CURDATE() ORDER BY slot_date, slot_time";
$result = mysqli_query($conn, $sql);

$slots = [];
while ($row = mysqli_fetch_assoc($result)) {
    $slots[] = $row;
}
echo json_encode($slots);
?>
