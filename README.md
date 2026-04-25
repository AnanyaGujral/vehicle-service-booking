# 🚗 Vehicle Service Booking System

A web-based application for booking and managing vehicle service appointments with intelligent scheduling and complete service history tracking.

**Mini Project | B.Tech IT | Web Technology**
**Author:** Ananya | **Enrollment:** 02214803123 | **Group:** 1

---

## 📋 Features

### Customer
- Register & login securely
- Add and manage multiple vehicles
- View available service slots in real time
- Book appointments (double-booking prevented automatically)
- Cancel appointments (slot is released immediately)
- View complete service history per vehicle

### Admin
- View all bookings across all customers
- Update appointment status (Pending → Confirmed → Completed → Cancelled)
- Enter service cost and remarks on completion
- Service history auto-logged when marked Completed
- Dashboard with total bookings, revenue, and customer count

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript (Fetch API) |
| Backend | PHP (MySQLi) |
| Database | MySQL |
| Fonts | Google Fonts (Rajdhani + Nunito) |
| Version Control | Git / GitHub |

---

## 📁 Project Structure

```
vehicle-service-booking/
│
├── index.php               # Login page
├── register.php            # Registration page
├── dashboard.php           # Customer dashboard
├── admin_dashboard.php     # Admin dashboard
│
├── css/
│   └── style.css           # Main stylesheet
│
├── js/
│   └── main.js             # All JavaScript (AJAX calls)
│
├── php/
│   ├── config.php          # DB connection + session start
│   ├── register.php        # Register logic
│   ├── login.php           # Login + session logic
│   ├── logout.php          # Session destroy + redirect
│   ├── add_vehicle.php     # Add vehicle logic
│   ├── book_appointment.php# Booking + conflict check
│   ├── cancel_appointment.php # Cancel + slot release
│   ├── get_slots.php       # AJAX: fetch available slots
│   └── update_status.php   # Admin: update status + log history
│
└── sql/
    └── database.sql        # Full DB schema + sample data
```

---

## ⚙️ Setup Instructions

### Prerequisites
- XAMPP / WAMP / LAMP installed
- PHP 7.4+
- MySQL 5.7+



## 📄 License

This project is submitted as an academic mini project for B.Tech IT — Web Technology at MAIT Delhi.
