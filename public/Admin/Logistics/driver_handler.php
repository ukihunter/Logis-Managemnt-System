<?php
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

header('Content-Type: application/json');

$conn = getDBConnection();
$response = ['success' => false, 'message' => ''];

// Handle different actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addDriver($conn);
        break;
    case 'edit':
        editDriver($conn);
        break;
    case 'delete':
        deleteDriver($conn);
        break;
    case 'get_all':
        getAllDrivers($conn);
        break;
    case 'get_stats':
        getDriverStats($conn);
        break;
    case 'get_single':
        getSingleDriver($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();

function addDriver($conn)
{
    global $response;

    try {
        // Generate unique employee ID
        $year = date('Y');
        $employee_id = null;
        $max_attempts = 100;
        $attempt = 0;

        // Keep trying until we get a unique employee ID
        while ($attempt < $max_attempts) {
            // Get the highest number for this year
            $stmt = $conn->prepare("SELECT employee_id FROM drivers WHERE employee_id LIKE ? ORDER BY employee_id DESC LIMIT 1");
            $pattern = "EMP-$year-%";
            $stmt->bind_param("s", $pattern);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $last_id = $row['employee_id'];
                // Extract the number from EMP-2026-001 format
                preg_match('/EMP-\d{4}-(\d+)/', $last_id, $matches);
                $next_number = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            } else {
                $next_number = 1;
            }

            $employee_id = sprintf("EMP-%s-%03d", $year, $next_number);
            $stmt->close();

            // Check if this ID already exists
            $check_stmt = $conn->prepare("SELECT id FROM drivers WHERE employee_id = ?");
            $check_stmt->bind_param("s", $employee_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_stmt->close();

            if ($check_result->num_rows == 0) {
                // This ID is unique, we can use it
                break;
            }

            $attempt++;
        }

        if ($attempt >= $max_attempts) {
            throw new Exception("Unable to generate unique employee ID");
        }

        // Prepare insert statement
        $stmt = $conn->prepare("INSERT INTO drivers (employee_id, full_name, phone_number, email, license_number, vehicle_type, vehicle_model, license_plate, distribution_centre, status, start_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $full_name = $_POST['full_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'] ?? '';
        $license_number = $_POST['license_number'];
        $vehicle_type = $_POST['vehicle_type'];
        $vehicle_model = $_POST['vehicle_model'] ?? '';
        $license_plate = $_POST['license_plate'];
        $distribution_centre = $_POST['distribution_centre'];
        $status = $_POST['status'] ?? 'active';
        $start_date = $_POST['start_date'];

        $stmt->bind_param(
            "sssssssssss",
            $employee_id,
            $full_name,
            $phone_number,
            $email,
            $license_number,
            $vehicle_type,
            $vehicle_model,
            $license_plate,
            $distribution_centre,
            $status,
            $start_date
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Driver added successfully';
            $response['driver_id'] = $conn->insert_id;
            $response['employee_id'] = $employee_id;
        } else {
            $response['message'] = 'Error adding driver: ' . $stmt->error;
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }

    echo json_encode($response);
}

function editDriver($conn)
{
    global $response;

    try {
        $stmt = $conn->prepare("UPDATE drivers SET full_name = ?, phone_number = ?, email = ?, license_number = ?, vehicle_type = ?, vehicle_model = ?, license_plate = ?, distribution_centre = ?, status = ?, start_date = ? WHERE id = ?");

        $full_name = $_POST['full_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'] ?? '';
        $license_number = $_POST['license_number'];
        $vehicle_type = $_POST['vehicle_type'];
        $vehicle_model = $_POST['vehicle_model'] ?? '';
        $license_plate = $_POST['license_plate'];
        $distribution_centre = $_POST['distribution_centre'];
        $status = $_POST['status'];
        $start_date = $_POST['start_date'];
        $id = $_POST['id'];

        $stmt->bind_param(
            "ssssssssssi",
            $full_name,
            $phone_number,
            $email,
            $license_number,
            $vehicle_type,
            $vehicle_model,
            $license_plate,
            $distribution_centre,
            $status,
            $start_date,
            $id
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Driver updated successfully';
        } else {
            $response['message'] = 'Error updating driver: ' . $stmt->error;
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }

    echo json_encode($response);
}

function deleteDriver($conn)
{
    global $response;

    try {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM drivers WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Driver deleted successfully';
        } else {
            $response['message'] = 'Error deleting driver: ' . $stmt->error;
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }

    echo json_encode($response);
}

function getAllDrivers($conn)
{
    try {
        $query = "SELECT * FROM drivers ORDER BY created_at DESC";

        // Add filters if provided
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $status = $_GET['status'];
            $stmt = $conn->prepare("SELECT * FROM drivers WHERE status = ? ORDER BY created_at DESC");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
        } else if (isset($_GET['search']) && $_GET['search'] !== '') {
            $search = '%' . $_GET['search'] . '%';
            $stmt = $conn->prepare("SELECT * FROM drivers WHERE full_name LIKE ? OR employee_id LIKE ? OR license_plate LIKE ? ORDER BY created_at DESC");
            $stmt->bind_param("sss", $search, $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();
        } else if (isset($_GET['centre']) && $_GET['centre'] !== '') {
            $centre = $_GET['centre'];
            $stmt = $conn->prepare("SELECT * FROM drivers WHERE distribution_centre = ? ORDER BY created_at DESC");
            $stmt->bind_param("s", $centre);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($query);
        }

        $drivers = [];
        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }

        echo json_encode(['success' => true, 'drivers' => $drivers]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function getDriverStats($conn)
{
    try {
        // Get total drivers
        $result = $conn->query("SELECT COUNT(*) as total FROM drivers");
        $total = $result->fetch_assoc()['total'];

        // Get active drivers
        $result = $conn->query("SELECT COUNT(*) as active FROM drivers WHERE status = 'active'");
        $active = $result->fetch_assoc()['active'];

        // Get inactive/on leave drivers
        $result = $conn->query("SELECT COUNT(*) as inactive FROM drivers WHERE status IN ('inactive', 'on_leave')");
        $inactive = $result->fetch_assoc()['inactive'];

        echo json_encode([
            'success' => true,
            'stats' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function getSingleDriver($conn)
{
    try {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'driver' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Driver not found']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
