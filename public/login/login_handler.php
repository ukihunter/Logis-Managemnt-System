<?php
// Start session
session_start();

// Include database configuration
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check if the request method is POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both username and password']);
        exit;
    }

    // Get database connection
    $conn = getDBConnection();

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, business_name, full_name, username, password, phone_number, address,province, user_type, status FROM users WHERE username = ? OR email = ?");

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    // Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if account is active
        if ($user['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Your account is not active. Please contact admin.']);
            exit;
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['business_name'] = $user['business_name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['logged_in'] = true;
            $_SESSION['address'] = $user['address'] ?? 'N/A';
            $_SESSION['contact_number'] = $user['contact_number'] ?? 'N/A';
            $_SESSION['province'] = $user['province'] ?? '';

            // Redirect based on user type
            $redirect_url = '';
            // Determine redirect URL based on user type
            switch ($user['user_type']) {
                //if it is customer redirect to customer dashboard
                case 'customer':
                    $redirect_url = '../Customer/Dashboard/dashboard.php';
                    break;
                //if it is admin or staff redirect to admin dashboard
                case 'admin':
                case 'staff':
                    $redirect_url = '../Admin/Dasboard/dasboard.php';
                    break;
                // Default redirect
                default:
                    $redirect_url = '../Customer/Dashboard/dashboard.php';
            }
            // Send success response
            echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect_url]);
        } else {
            // Invalid password
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
        // End of password verification
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }

    $stmt->close();
    $conn->close();
    // End of POST request handling
} else {

    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
