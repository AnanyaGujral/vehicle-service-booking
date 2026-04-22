-- Vehicle Service Booking System
-- Database: vehicle_service_db
-- Author: Ananya | Enrollment: 02214803123

CREATE DATABASE IF NOT EXISTS vehicle_service_db;
USE vehicle_service_db;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vehicles Table
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_name VARCHAR(100) NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL UNIQUE,
    vehicle_type ENUM('Car', 'Bike', 'Truck', 'SUV') NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Service Slots Table
CREATE TABLE service_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    is_available TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_slot (slot_date, slot_time)
);

-- Appointments Table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    slot_id INT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES service_slots(id) ON DELETE CASCADE
);

-- Service History Table
CREATE TABLE service_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    user_id INT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    service_date DATE NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0.00,
    remarks TEXT,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Admin User (password: admin123)
INSERT INTO users (name, email, password, phone, role)
VALUES ('Admin', 'admin@vehicleservice.com', MD5('admin123'), '9999999999', 'admin');

-- Sample Service Slots
INSERT INTO service_slots (slot_date, slot_time) VALUES
('2025-05-01', '09:00:00'), ('2025-05-01', '10:00:00'), ('2025-05-01', '11:00:00'),
('2025-05-01', '14:00:00'), ('2025-05-01', '15:00:00'), ('2025-05-02', '09:00:00'),
('2025-05-02', '10:00:00'), ('2025-05-02', '11:00:00'), ('2025-05-02', '14:00:00'),
('2025-05-02', '15:00:00');
