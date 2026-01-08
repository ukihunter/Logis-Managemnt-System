-- Drivers table for logistics management
CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    license_number VARCHAR(100) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    vehicle_model VARCHAR(100),
    license_plate VARCHAR(20) NOT NULL,
    distribution_centre VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    start_date DATE NOT NULL,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample drivers for testing
INSERT INTO drivers (employee_id, full_name, phone_number, email, license_number, vehicle_type, vehicle_model, license_plate, distribution_centre, status, start_date) VALUES
('EMP-2024-042', 'Michael Chen', '+65 9123 4567', 'michael.chen@logis.com', 'DL-2024-042', 'truck', 'Hino 300', 'GBA 1234 A', 'West RDC - Jurong', 'active', '2024-03-15'),
('EMP-2023-118', 'Sarah Tan', '+65 8876 5432', 'sarah.tan@logis.com', 'DL-2023-118', 'van', 'Toyota Hiace', 'GBD 5678 Z', 'North RDC - Woodlands', 'active', '2023-06-20'),
('EMP-2022-092', 'John Lim', '+65 9988 7766', 'john.lim@logis.com', 'DL-2022-092', 'motorcycle', 'Honda PCX', 'FBJ 9999 X', 'East RDC - Changi', 'inactive', '2022-11-10'),
('EMP-2024-005', 'David Ng', '+65 8123 9876', 'david.ng@logis.com', 'DL-2024-005', 'truck', 'Isuzu N-Series', 'GBC 4321 B', 'West RDC - Jurong', 'active', '2024-01-08'),
('EMP-2023-221', 'Emily Wong', '+65 9654 3210', 'emily.wong@logis.com', 'DL-2023-221', 'van', 'Nissan NV350', 'GBA 5678 M', 'North RDC - Woodlands', 'on_leave', '2023-09-15');
