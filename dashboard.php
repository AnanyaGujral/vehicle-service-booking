<?php
require 'php/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('Location: index.php'); exit();
}
$uid = $_SESSION['user_id'];

$vehicles     = mysqli_query($conn, "SELECT * FROM vehicles WHERE user_id='$uid'");
$appointments = mysqli_query($conn, "SELECT a.*, v.vehicle_name, v.vehicle_number, s.slot_date, s.slot_time
    FROM appointments a
    JOIN vehicles v ON a.vehicle_id = v.id
    JOIN service_slots s ON a.slot_id = s.id
    WHERE a.user_id='$uid' ORDER BY a.created_at DESC");
$history      = mysqli_query($conn, "SELECT h.*, v.vehicle_name, v.vehicle_number
    FROM service_history h JOIN vehicles v ON h.vehicle_id = v.id
    WHERE h.user_id='$uid' ORDER BY h.completed_at DESC");

$totalV = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM vehicles WHERE user_id='$uid'"));
$totalA = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM appointments WHERE user_id='$uid' AND status='Confirmed'"));
$totalH = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM service_history WHERE user_id='$uid'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Vehicle Service Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">&#128663; V<span>Service</span></div>
    <div class="nav-links">
        <span style="color:#aaa;font-size:14px">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="php/logout.php" class="btn-logout">Logout</a>
    </div>
</nav>
<div class="page-wrapper">
    <aside class="sidebar">
        <a href="#overview"   class="active"><span class="icon">&#9685;</span> Overview</a>
        <a href="#vehicles"  ><span class="icon">&#128663;</span> My Vehicles</a>
        <a href="#book"      ><span class="icon">&#128197;</span> Book Service</a>
        <a href="#appointments"><span class="icon">&#128203;</span> Appointments</a>
        <a href="#history"   ><span class="icon">&#128196;</span> Service History</a>
    </aside>
    <main class="main-content">

        <!-- Stats -->
        <div class="stats-row" id="overview">
            <div class="stat-card"><div class="number"><?= $totalV ?></div><div class="label">Vehicles Registered</div></div>
            <div class="stat-card"><div class="number"><?= $totalA ?></div><div class="label">Active Appointments</div></div>
            <div class="stat-card"><div class="number"><?= $totalH ?></div><div class="label">Services Completed</div></div>
        </div>

        <!-- Add Vehicle -->
        <div class="card" id="vehicles">
            <h2>&#128663; Add Vehicle</h2>
            <div class="alert" id="vehicleAlert"></div>
            <form id="vehicleForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Vehicle Name</label>
                        <input type="text" name="vehicle_name" placeholder="e.g. My Swift" required>
                    </div>
                    <div class="form-group">
                        <label>Registration Number</label>
                        <input type="text" name="vehicle_number" placeholder="e.g. DL01AB1234" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Vehicle Type</label>
                        <select name="vehicle_type" required>
                            <option value="">Select Type</option>
                            <option>Car</option><option>Bike</option><option>Truck</option><option>SUV</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" placeholder="e.g. Maruti">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" name="model" placeholder="e.g. Swift VXI">
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="number" name="year" placeholder="e.g. 2020" min="1990" max="2025">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Vehicle</button>
            </form>

            <!-- My Vehicles Table -->
            <?php if (mysqli_num_rows($vehicles) > 0): ?>
            <h2 style="margin-top:24px">My Vehicles</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Name</th><th>Reg. No.</th><th>Type</th><th>Brand</th><th>Model</th><th>Year</th></tr></thead>
                    <tbody>
                    <?php $i=1; while($v = mysqli_fetch_assoc($vehicles)): ?>
                        <tr><td><?=$i++?></td><td><?=htmlspecialchars($v['vehicle_name'])?></td><td><?=htmlspecialchars($v['vehicle_number'])?></td><td><?=$v['vehicle_type']?></td><td><?=htmlspecialchars($v['brand'])?></td><td><?=htmlspecialchars($v['model'])?></td><td><?=$v['year']?></td></tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Book Appointment -->
        <div class="card" id="book">
            <h2>&#128197; Book Service Appointment</h2>
            <div class="alert" id="bookAlert"></div>
            <form id="bookForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Vehicle</label>
                        <select name="vehicle_id" required>
                            <option value="">-- Select Vehicle --</option>
                            <?php
                            $vlist = mysqli_query($conn, "SELECT * FROM vehicles WHERE user_id='$uid'");
                            while($v = mysqli_fetch_assoc($vlist)):
                            ?>
                            <option value="<?=$v['id']?>"><?=htmlspecialchars($v['vehicle_name'])?> (<?=htmlspecialchars($v['vehicle_number'])?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Available Slot</label>
                        <select name="slot_id" id="slot_id" required><option value="">Loading slots...</option></select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Service Type</label>
                    <select name="service_type" required>
                        <option value="">Select Service</option>
                        <option>Oil Change</option>
                        <option>Tyre Rotation</option>
                        <option>Brake Inspection</option>
                        <option>Full Service</option>
                        <option>Battery Check</option>
                        <option>AC Service</option>
                        <option>Wheel Alignment</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Additional Description</label>
                    <textarea name="description" rows="3" placeholder="Describe any specific issues..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </form>
        </div>

        <!-- Appointments -->
        <div class="card" id="appointments">
            <h2>&#128203; My Appointments</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Vehicle</th><th>Service</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php $i=1; while($a = mysqli_fetch_assoc($appointments)): ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=htmlspecialchars($a['vehicle_name'])?><br><small><?=htmlspecialchars($a['vehicle_number'])?></small></td>
                            <td><?=htmlspecialchars($a['service_type'])?></td>
                            <td><?=$a['slot_date']?></td>
                            <td><?=substr($a['slot_time'],0,5)?></td>
                            <td><span class="badge badge-<?=strtolower($a['status'])?>"><?=$a['status']?></span></td>
                            <td>
                                <?php if($a['status'] == 'Pending' || $a['status'] == 'Confirmed'): ?>
                                <button class="btn btn-danger btn-sm" onclick="cancelAppointment(<?=$a['id']?>)">Cancel</button>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service History -->
        <div class="card" id="history">
            <h2>&#128196; Service History</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Vehicle</th><th>Service</th><th>Date</th><th>Cost (&#8377;)</th><th>Remarks</th></tr></thead>
                    <tbody>
                    <?php $i=1; $found=false; while($h = mysqli_fetch_assoc($history)): $found=true; ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=htmlspecialchars($h['vehicle_name'])?><br><small><?=htmlspecialchars($h['vehicle_number'])?></small></td>
                            <td><?=htmlspecialchars($h['service_type'])?></td>
                            <td><?=$h['service_date']?></td>
                            <td><?=number_format($h['cost'],2)?></td>
                            <td><?=htmlspecialchars($h['remarks'] ?: '—')?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if(!$found): ?>
                        <tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px">No service history yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
<script src="js/main.js"></script>
</body>
</html>
