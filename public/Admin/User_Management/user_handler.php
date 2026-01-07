<?php
session_start();
require_once '../../../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin or staff
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$conn = getDBConnection();

// Handle different actions
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    // Add new user
    $user_type = $_POST['user_type'] ?? 'customer';
    $business_name = $_POST['business_name'] ?? null;
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone_number = $_POST['phone_number'] ?? null;
    $address = $_POST['address'] ?? null;
    $province = $_POST['province'] ?? null;
    $status = $_POST['status'] ?? 'pending';

    // Validate required fields
    if (empty($full_name) || empty($email) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }

    // Check if username or email already exists
    $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $insertQuery = "INSERT INTO users (business_name, full_name, email, username, password, phone_number, address, province, user_type, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssssssss", $business_name, $full_name, $email, $username, $hashed_password, $phone_number, $address, $province, $user_type, $status);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . $conn->error]);
    }
} elseif ($action === 'update') {
    // Update existing user
    $user_id = $_POST['user_id'] ?? 0;
    $user_type = $_POST['user_type'] ?? 'customer';
    $business_name = $_POST['business_name'] ?? null;
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $phone_number = $_POST['phone_number'] ?? null;
    $address = $_POST['address'] ?? null;
    $province = $_POST['province'] ?? null;
    $status = $_POST['status'] ?? 'active';

    // Validate required fields
    if (empty($user_id) || empty($full_name) || empty($email) || empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }

    // Check if username or email already exists for other users
    $checkQuery = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit();
    }

    // Update user - check if password is being updated
    if (!empty($_POST['password'])) {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET business_name = ?, full_name = ?, email = ?, username = ?, password = ?, 
                        phone_number = ?, address = ?, province = ?, user_type = ?, status = ?, updated_at = NOW() 
                        WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param(
            "ssssssssssi",
            $business_name,
            $full_name,
            $email,
            $username,
            $hashed_password,
            $phone_number,
            $address,
            $province,
            $user_type,
            $status,
            $user_id
        );
    } else {
        $updateQuery = "UPDATE users SET business_name = ?, full_name = ?, email = ?, username = ?, 
                        phone_number = ?, address = ?, province = ?, user_type = ?, status = ?, updated_at = NOW() 
                        WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param(
            "sssssssssi",
            $business_name,
            $full_name,
            $email,
            $username,
            $phone_number,
            $address,
            $province,
            $user_type,
            $status,
            $user_id
        );
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $conn->error]);
    }
} elseif ($action === 'delete') {
    // Delete user
    $user_id = $_POST['user_id'] ?? 0;

    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit();
    }

    // Don't allow deleting yourself
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
        exit();
    }

    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user: ' . $conn->error]);
    }
} elseif ($action === 'get') {
    // Get single user data for editing
    $user_id = $_POST['user_id'] ?? 0;

    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit();
    }

    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Don't send password
        unset($user['password']);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
