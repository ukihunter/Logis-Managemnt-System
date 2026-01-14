<?php
// db and session includes
require_once '../../../config/database.php';
require_once '../../../config/admin_session.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
// Route actions
switch ($action) {
    case 'get_all_orders':
        getAllOrders();
        break;
    case 'get_order_details':
        getOrderDetails();
        break;
    case 'update_status':
        updateOrderStatus();
        break;
    case 'assign_driver':
        assignDriver();
        break;
    case 'get_order_stats':
        getOrderStats();
        break;
    case 'get_available_drivers':
        getAvailableDrivers();
        break;
    case 'save_delivery_notes':
        saveDeliveryNotes();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
// Function definitions
function getAllOrders()
{
    $conn = getDBConnection();

    try {
        $query = "SELECT 
                    o.id,
                    o.order_number,
                    o.customer_name,
                    o.business_name,
                    o.shipping_city,
                    o.total_amount,
                    o.order_status,
                    o.payment_status,
                    o.created_at,
                    o.driver_id,
                    d.full_name as driver_name,
                    d.employee_id as driver_employee_id,
                    COUNT(oi.id) as total_items
                FROM orders o
                LEFT JOIN drivers d ON o.driver_id = d.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC";

        $result = $conn->query($query);
        $orders = [];

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        echo json_encode(['success' => true, 'orders' => $orders]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching orders: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
// Fetch detailed information for a specific order
function getOrderDetails()
{
    $conn = getDBConnection();
    $orderId = $_GET['order_id'] ?? 0;

    try {
        // Get order details
        $orderQuery = "SELECT 
                        o.*,
                        d.full_name as driver_name,
                        d.employee_id as driver_employee_id,
                        d.phone_number as driver_phone,
                        d.license_plate as driver_vehicle,
                        u.username as customer_username
                    FROM orders o
                    LEFT JOIN drivers d ON o.driver_id = d.id
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?";

        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        // Get order items
        $itemsQuery = "SELECT 
                        oi.*,
                        p.stock_level as stock_quantity
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";

        $stmt = $conn->prepare($itemsQuery);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get status history
        $historyQuery = "SELECT 
                            osh.*,
                            u.username as created_by_name
                        FROM order_status_history osh
                        LEFT JOIN users u ON osh.created_by = u.id
                        WHERE osh.order_id = ?
                        ORDER BY osh.created_at ASC";

        $stmt = $conn->prepare($historyQuery);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $order['items'] = $items;
        $order['status_history'] = $history;

        echo json_encode(['success' => true, 'order' => $order]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching order details: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
// Update order status and log history
function updateOrderStatus()
{
    $conn = getDBConnection();
    $orderId = $_POST['order_id'] ?? 0;
    $newStatus = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $adminId = $_SESSION['user_id'] ?? null;

    try {
        $conn->begin_transaction();

        // Update order status
        $updateQuery = "UPDATE orders SET order_status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('si', $newStatus, $orderId);
        $stmt->execute();

        // Add to status history
        $historyQuery = "INSERT INTO order_status_history (order_id, status, notes, created_by) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($historyQuery);
        $stmt->bind_param('issi', $orderId, $newStatus, $notes, $adminId);
        $stmt->execute();

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error updating order status: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
// Assign or unassign driver to/from order
function assignDriver()
{
    $conn = getDBConnection();
    $orderId = $_POST['order_id'] ?? 0;
    $driverId = $_POST['driver_id'] ?? null;

    // Convert empty string to null
    if ($driverId === '' || $driverId === 'null') {
        $driverId = null;
    }

    try {
        $query = "UPDATE orders SET driver_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $driverId, $orderId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Driver assigned successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error assigning driver: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
// Get order statistics
function getOrderStats()
{
    $conn = getDBConnection();

    try {
        $query = "SELECT 
                    COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN order_status = 'processing' THEN 1 END) as processing_count,
                    COUNT(CASE WHEN order_status = 'packed' THEN 1 END) as packed_count,
                    COUNT(CASE WHEN order_status = 'shipped' THEN 1 END) as shipped_count,
                    COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered_count,
                    COUNT(CASE WHEN order_status = 'cancelled' THEN 1 END) as cancelled_count,
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN order_status IN ('delivered') THEN 1 ELSE 0 END) / COUNT(*) * 100 as fill_rate
                FROM orders";

        $result = $conn->query($query);
        $stats = $result->fetch_assoc();

        echo json_encode(['success' => true, 'stats' => $stats]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching order stats: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
// Get list of available drivers
function getAvailableDrivers()
{
    $conn = getDBConnection();

    try {
        $query = "SELECT id, employee_id, full_name, phone_number, license_plate, distribution_centre, status 
                  FROM drivers 
                  WHERE status = 'active'
                  ORDER BY full_name ASC";

        $result = $conn->query($query);
        $drivers = [];

        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }

        echo json_encode(['success' => true, 'drivers' => $drivers]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching drivers: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
// Save delivery notes for an order
function saveDeliveryNotes()
{
    $conn = getDBConnection();

    try {
        $orderId = $_POST['order_id'] ?? null;
        $notes = $_POST['notes'] ?? '';

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID is required']);
            return;
        }

        // Check if order status allows editing notes (only pending, processing, packed)
        $checkQuery = "SELECT order_status FROM orders WHERE id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        if (!in_array($order['order_status'], ['pending', 'processing', 'packed'])) {
            echo json_encode(['success' => false, 'message' => 'Cannot edit notes for orders that are shipped or delivered']);
            return;
        }

        // Update admin notes
        $updateQuery = "UPDATE orders SET admin_notes = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('si', $notes, $orderId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Delivery notes saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save delivery notes']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving delivery notes: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
