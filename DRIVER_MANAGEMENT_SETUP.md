# Driver Management Setup Instructions

## Database Setup

1. Open phpMyAdmin or your MySQL client
2. Navigate to your `logis_db` database
3. Run the SQL script from `db/drivers_schema.sql` to create the drivers table

   OR

   You can run this SQL directly:

```sql
-- Run this in your logis_db database
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
```

## Features Implemented

### ✅ Database-Driven Data

- All driver data now comes from the `drivers` table in the database
- No more hardcoded data in the page
- Real-time statistics from database

### ✅ Statistics Cards

The three cards at the top now show real data:

- **Total Drivers**: Count of all drivers in the database
- **Active Now**: Count of drivers with 'active' status
- **Inactive/Leave**: Count of drivers with 'inactive' or 'on_leave' status

### ✅ Right Side Panel

- Opens when clicking "Add New Driver" button
- Form includes all necessary fields:
  - Personal Information (Name, Phone, Email, License Number)
  - Vehicle Assignment (Type, Model, License Plate, Distribution Centre)
  - Employment Details (Start Date, Status)
- Employee ID is auto-generated in format: EMP-YYYY-XXX

### ✅ CRUD Operations

- **Create**: Add new drivers via the form
- **Read**: View all drivers in the table
- **Update**: Click edit icon to modify driver details
- **Delete**: Click delete icon to remove a driver (with confirmation)

### ✅ Driver Handler API

Location: `public/Admin/Logistics/driver_handler.php`

Available actions:

- `add` - Add a new driver
- `edit` - Update existing driver
- `delete` - Remove a driver
- `get_all` - Get all drivers (with optional filters)
- `get_single` - Get single driver by ID
- `get_stats` - Get driver statistics

## Usage

1. **Add a Driver**:

   - Click "Add New Driver" button
   - Fill in the form
   - Click "Add Driver" button
   - Page will reload with the new driver

2. **Edit a Driver**:

   - Click the edit (pencil) icon on any driver row
   - Modify the details in the form
   - Click "Update Driver" button

3. **Delete a Driver**:
   - Click the delete (trash) icon on any driver row
   - Confirm the deletion
   - Driver will be removed from the database

## File Structure

```
public/Admin/Logistics/
├── driver_mangemnt.php      # Main driver management page
├── driver_handler.php       # Backend API for CRUD operations
└── js/
    └── (existing files)

db/
└── drivers_schema.sql       # Database schema and sample data
```

## Notes

- The right side panel slides in when adding/editing drivers
- Form validation is handled on both client and server side
- AJAX is used for smooth operations without page reload (except after successful add/edit for data refresh)
- Notifications appear in the top-right corner for success/error messages
- All data is properly sanitized to prevent SQL injection
