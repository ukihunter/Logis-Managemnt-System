<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

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
    $stmt = $conn->prepare("SELECT id, business_name, full_name, username, password, user_type, status FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

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

            // Redirect based on user type
            $redirect_url = '';
            switch ($user['user_type']) {
                case 'customer':
                    $redirect_url = '../customer/dashboard/dashboard.php';
                    break;
                case 'admin':
                    $redirect_url = '../admin/dashboard/dashboard.php';
                    break;
                case 'staff':
                    $redirect_url = '../staff/dashboard/dashboard.php';
                    break;
                default:
                    $redirect_url = '../customer/dashboard/dashboard.php';
            }

            echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect_url]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
