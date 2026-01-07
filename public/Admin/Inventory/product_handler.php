<?php
session_start();
require_once '../../../config/database.php';

// Clean any output buffers to prevent HTML/whitespace in JSON response
if (ob_get_level()) {
    ob_clean();
}

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
    // Add new product
    $sku = $_POST['sku'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $brand = $_POST['brand'] ?? null;
    $unit_price = $_POST['unit_price'] ?? 0;
    $carton_quantity = $_POST['carton_quantity'] ?? 1;
    $stock_level = $_POST['stock_level'] ?? 0;
    $max_level = $_POST['max_level'] ?? 0;
    $allocated = $_POST['allocated'] ?? 0;
    $status = $_POST['status'] ?? 'active';
    $offer_label = $_POST['offer_label'] ?? null;
    $discount_percentage = $_POST['discount_percentage'] ?? 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Validate required fields
    if (empty($sku) || empty($name) || empty($category) || empty($unit_price)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }

    // Check if SKU already exists
    $checkQuery = "SELECT id FROM products WHERE sku = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'SKU already exists']);
        exit();
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../assest/product/';

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed']);
            exit();
        }

        // Generate unique filename
        $filename = $sku . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
            $image_path = 'assest/product/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }

    // Calculate carton price
    $carton_price = $unit_price * $carton_quantity;

    // Insert new product
    $insertQuery = "INSERT INTO products (sku, name, description, category, brand, image_path, unit_price, carton_quantity, carton_price, stock_level, max_level, allocated, status, offer_label, discount_percentage, is_featured, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($insertQuery);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssssdidiiissdi", $sku, $name, $description, $category, $brand, $image_path, $unit_price, $carton_quantity, $carton_price, $stock_level, $max_level, $allocated, $status, $offer_label, $discount_percentage, $is_featured);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product: ' . $stmt->error]);
    }
} elseif ($action === 'update') {
    // Update existing product
    $product_id = $_POST['product_id'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $brand = $_POST['brand'] ?? null;
    $unit_price = $_POST['unit_price'] ?? 0;
    $carton_quantity = $_POST['carton_quantity'] ?? 1;
    $stock_level = $_POST['stock_level'] ?? 0;
    $max_level = $_POST['max_level'] ?? 0;
    $allocated = $_POST['allocated'] ?? 0;
    $status = $_POST['status'] ?? 'active';
    $offer_label = $_POST['offer_label'] ?? null;
    $discount_percentage = $_POST['discount_percentage'] ?? 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Validate required fields
    if (empty($product_id) || empty($sku) || empty($name) || empty($category) || empty($unit_price)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit();
    }

    // Check if SKU already exists for other products
    $checkQuery = "SELECT id FROM products WHERE sku = ? AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $sku, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'SKU already exists']);
        exit();
    }

    // Get current image path
    $getImageQuery = "SELECT image_path FROM products WHERE id = ?";
    $stmt = $conn->prepare($getImageQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_product = $result->fetch_assoc();
    $image_path = $current_product['image_path'];

    // Handle image upload if new image provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../assest/product/';

        // Delete old image if exists
        if ($image_path && file_exists('../../../' . $image_path)) {
            unlink('../../../' . $image_path);
        }

        $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed']);
            exit();
        }

        $filename = $sku . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
            $image_path = 'assest/product/' . $filename;
        }
    }

    // Calculate carton price
    $carton_price = $unit_price * $carton_quantity;

    // Update product
    $updateQuery = "UPDATE products SET sku = ?, name = ?, description = ?, category = ?, brand = ?, image_path = ?, 
                    unit_price = ?, carton_quantity = ?, carton_price = ?, stock_level = ?, max_level = ?, allocated = ?, 
                    status = ?, offer_label = ?, discount_percentage = ?, is_featured = ?, updated_at = NOW() 
                    WHERE id = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssdidiisssdii", $sku, $name, $description, $category, $brand, $image_path, $unit_price, $carton_quantity, $carton_price, $stock_level, $max_level, $allocated, $status, $offer_label, $discount_percentage, $is_featured, $product_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . $conn->error]);
    }
} elseif ($action === 'delete') {
    // Delete product
    $product_id = $_POST['product_id'] ?? 0;

    if (empty($product_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit();
    }

    // Get image path before deleting
    $getImageQuery = "SELECT image_path FROM products WHERE id = ?";
    $stmt = $conn->prepare($getImageQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Delete image file if exists
    if ($product && $product['image_path'] && file_exists('../../../' . $product['image_path'])) {
        unlink('../../../' . $product['image_path']);
    }

    $deleteQuery = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product: ' . $conn->error]);
    }
} elseif ($action === 'get') {
    // Get single product data for editing
    $product_id = $_POST['product_id'] ?? 0;

    if (empty($product_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit();
    }

    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
