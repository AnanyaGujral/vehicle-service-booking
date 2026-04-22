<?php
require 'php/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: index.php'); exit();
}

$allAppointments = mysqli_query($conn,
    "SELECT a.*, u.name AS customer_name, u.phone, v.vehicle_name, v.vehicle_number,
            s.slot_date, s.slot_time
     FROM appointments a
     JOIN users u ON a.user_id = u.id
     JOIN vehicles v ON a.vehicle_id = v.id
     JOIN service_slots s ON a.slot_id = s.id
     ORDER BY a.created_at DESC");

$allHistory = mysqli_query($conn,
    "SELECT h.*, u.name AS customer_name, v.vehicle_name, v.vehicle_number
     FROM service_history h
     JOIN users u ON h.user_id = u.id
     JOIN vehicles v ON h.vehicle_id = v.id
     ORDER BY h.completed_at DESC");

$totalBookings  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM appointments"))['c'];
$totalCompleted = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM appointments WHERE status='Completed'"))['c'];
$totalCustomers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users WHERE role='customer'"))['c'];
$totalRevenue   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(cost) c FROM service_history"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Vehicle Service Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">&#128663; V<span>Service</span> <span style="font-size:12px;color:#e94560;margin-left:8px">ADMIN</span></div>
    <div class="nav-links">
        <span style="color:#aaa;font-size:14px">Admin: <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="php/logout.php" class="btn-logout">Logout</a>
    </div>
</nav>
<div class="page-wrapper">
    <aside class="sidebar">
        <a href="#overview"     class="active"><span class="icon">&#9685;</span> Overview</a>
        <a href="#appointments"><span class="icon">&#128203;</span> All Bookings</a>
        <a href="#history"     ><span class="icon">&#128196;</span> Service History</a>
    </aside>
    <main class="main-content">

        <!-- Stats -->
        <div class="stats-row" id="overview">
            <div class="stat-card"><div class="number"><?=$totalBookings?></div><div class="label">Total Bookings</div></div>
            <div class="stat-card"><div class="number"><?=$totalCompleted?></div><div class="label">Completed</div></div>
            <div class="stat-card"><div class="number"><?=$totalCustomers?></div><div class="label">Customers</div></div>
            <div class="stat-card"><div class="number">&#8377;<?=number_format($totalRevenue,0)?></div><div class="label">Total Revenue</div></div>
        </div>

        <!-- All Appointments -->
        <div class="card" id="appointments">
            <h2>&#128203; All Appointments</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Service</th><th>Date</th><th>Time</th><th>Status</th><th>Update</th></tr>
                    </thead>
                    <tbody>
                    <?php $i=1; while($a = mysqli_fetch_assoc($allAppointments)): ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=htmlspecialchars($a['customer_name'])?><br><small><?=htmlspecialchars($a['phone'])?></small></td>
                            <td><?=htmlspecialchars($a['vehicle_name'])?><br><small><?=htmlspecialchars($a['vehicle_number'])?></small></td>
                            <td><?=htmlspecialchars($a['service_type'])?></td>
                            <td><?=$a['slot_date']?></td>
                            <td><?=substr($a['slot_time'],0,5)?></td>
                            <td><span class="badge badge-<?=strtolower($a['status'])?>"><?=$a['status']?></span></td>
                            <td>
                                <select id="status_<?=$a['id']?>">
                                    <option <?=$a['status']=='Pending'?'selected':''?>>Pending</option>
                                    <option <?=$a['status']=='Confirmed'?'selected':''?>>Confirmed</option>
                                    <option <?=$a['status']=='Completed'?'selected':''?>>Completed</option>
                                    <option <?=$a['status']=='Cancelled'?'selected':''?>>Cancelled</option>
                                </select>
                                <?php if($a['status'] != 'Completed'): ?>
                                <br><input type="number" id="cost_<?=$a['id']?>" placeholder="Cost &#8377;" style="width:90px;margin-top:4px;padding:4px 8px;border:1px solid #ddd;border-radius:4px;font-size:12px">
                                <input type="text" id="remarks_<?=$a['id']?>" placeholder="Remarks" style="width:120px;padding:4px 8px;border:1px solid #ddd;border-radius:4px;font-size:12px;margin-top:4px">
                                <?php endif; ?>
                                <br><button class="btn btn-success btn-sm" style="margin-top:6px" onclick="updateStatus(<?=$a['id']?>)">Update</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service History -->
        <div class="card" id="history">
            <h2>&#128196; Complete Service History</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Service</th><th>Date</th><th>Cost (&#8377;)</th><th>Remarks</th></tr></thead>
                    <tbody>
                    <?php $i=1; $found=false; while($h = mysqli_fetch_assoc($allHistory)): $found=true; ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=htmlspecialchars($h['customer_name'])?></td>
                            <td><?=htmlspecialchars($h['vehicle_name'])?><br><small><?=htmlspecialchars($h['vehicle_number'])?></small></td>
                            <td><?=htmlspecialchars($h['service_type'])?></td>
                            <td><?=$h['service_date']?></td>
                            <td><?=number_format($h['cost'],2)?></td>
                            <td><?=htmlspecialchars($h['remarks'] ?: '—')?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if(!$found): ?>
                        <tr><td colspan="7" style="text-align:center;color:#aaa;padding:20px">No history records yet.</td></tr>
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
