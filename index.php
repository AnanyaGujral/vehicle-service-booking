<?php
require 'php/config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['user_role'] == 'admin' ? 'admin_dashboard.php' : 'dashboard.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Vehicle Service Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <h1>&#128663; <span>VService</span></h1>
        <p class="sub">Vehicle Service Booking System — Login to continue</p>
        <div class="alert" id="loginAlert"></div>
        <form id="loginForm">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Login</button>
        </form>
        <div class="switch-link">Don't have an account? <a href="register.php">Register here</a></div>
    </div>
</div>
<script src="js/main.js"></script>
</body>
</html>
