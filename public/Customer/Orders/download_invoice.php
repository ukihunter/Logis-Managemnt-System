<?php
// connction to database and session details
require_once '../../../config/database.php';
require_once '../../../config/session_Detils.php';

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    die('Order ID not specified');
}

// Sanitize and validate order_id
$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

//  coocntion start
$conn = getDBConnection();

// Get order details with security check
$order_query = "SELECT o.*, u.business_name, u.full_name, u.phone_number, u.address 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// order not found or does not belong to user

if ($result->num_rows === 0) {
    die('Order not found');
}

// Fetch order data
$order = $result->fetch_assoc();

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

// Close the database connection
$conn->close();

// Generate HTML invoice
?>
<!DOCTYPE html>
<html>

<!-- template fot the invoice  -->

<head>
    <meta charset="utf-8">
    <title>Invoice - <?php echo htmlspecialchars($order['order_number']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }

        .header {
            border-bottom: 2px solid #22c55e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #22c55e;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-block {
            width: 48%;
        }

        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row.grand-total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 18px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-name">IslandDistro</div>
            <div>Wholesale Distribution Services</div>
        </div>

        <div class="invoice-title">INVOICE</div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-block">
                    <div class="info-label">Invoice To:</div>
                    <div><?php echo htmlspecialchars($order['business_name']); ?></div>
                    <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
                    <div><?php echo htmlspecialchars($order['customer_email']); ?></div>
                    <div><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                </div>
                <div class="info-block">
                    <div class="info-label">Invoice Details:</div>
                    <div><strong>Invoice #:</strong> <?php echo htmlspecialchars($order['order_number']); ?></div>
                    <div><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                    <div><strong>Status:</strong> <?php echo ucfirst($order['payment_status']); ?></div>
                </div>
            </div>

            <div class="info-block">
                <div class="info-label">Shipping Address:</div>
                <div><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                <?php if ($order['shipping_city']): ?>
                    <div><?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_province'] ?? ''); ?> <?php echo htmlspecialchars($order['shipping_postal_code'] ?? ''); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>SKU</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['product_sku']); ?></td>
                        <td class="text-right"><?php echo $item['quantity']; ?></td>
                        <td class="text-right">Rs. <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="text-right">Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rs. <?php echo number_format($order['subtotal'], 2); ?></span>
            </div>
            <?php if ($order['tax_amount'] > 0): ?>
                <div class="total-row">
                    <span>Tax:</span>
                    <span>Rs. <?php echo number_format($order['tax_amount'], 2); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($order['shipping_fee'] > 0): ?>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span>Rs. <?php echo number_format($order['shipping_fee'], 2); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($order['discount_amount'] > 0): ?>
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-Rs. <?php echo number_format($order['discount_amount'], 2); ?></span>
                </div>
            <?php endif; ?>
            <div class="total-row grand-total">
                <span>Total:</span>
                <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>

        <?php if ($order['customer_notes']): ?>
            <div class="info-section">
                <div class="info-label">Notes:</div>
                <div><?php echo htmlspecialchars($order['customer_notes']); ?></div>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>IslandDistro Inc. | wholesale@islanddistro.com</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>