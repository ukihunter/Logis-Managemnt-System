<?php
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$download = isset($_GET['download']) ? (int)$_GET['download'] : 0;

if ($order_id === 0) {
    die('Invalid order ID');
}

$conn = getDBConnection();

// Get order details
$orderQuery = "SELECT o.*, u.business_name, u.full_name, u.phone_number, u.email, u.address as billing_address
               FROM orders o
               JOIN users u ON o.user_id = u.id
               WHERE o.id = ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die('Order not found');
}

// If download parameter is set, trigger download
if ($download === 1) {
    $filename = 'Invoice-' . $order['order_number'] . '.html';
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
}

// Get order items
$itemsQuery = "SELECT oi.*, p.name as product_name, p.sku
               FROM order_items oi
               JOIN products p ON oi.product_id = p.id
               WHERE oi.order_id = ?";
$stmt = $conn->prepare($itemsQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo htmlspecialchars($order['order_number']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #fff;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .header {
            border-bottom: 3px solid #11d452;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #11d452;
            font-size: 32px;
            margin-bottom: 5px;
        }

        .header .company-info {
            color: #666;
            font-size: 14px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-info div {
            flex: 1;
        }

        .invoice-info h3 {
            color: #333;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }

        .invoice-info p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }

        .invoice-details {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .invoice-details table {
            width: 100%;
        }

        .invoice-details td {
            padding: 5px;
            font-size: 13px;
        }

        .invoice-details td:first-child {
            font-weight: bold;
            color: #333;
            width: 150px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.items thead {
            background: #11d452;
            color: white;
        }

        table.items th {
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
        }

        table.items td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        table.items tbody tr:hover {
            background: #f5f5f5;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .totals table {
            width: 100%;
        }

        .totals td {
            padding: 8px;
            font-size: 14px;
        }

        .totals td:first-child {
            text-align: right;
            color: #666;
        }

        .totals td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .totals .grand-total {
            background: #11d452;
            color: white;
            font-size: 16px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            color: #999;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-packed {
            background: #e9d5ff;
            color: #6b21a8;
        }

        .status-shipped {
            background: #ddd6fe;
            color: #5b21b6;
        }

        .status-delivered {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .print-btn {
            background: #11d452;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .print-btn:hover {
            background: #0ebf49;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <button class="print-btn no-print" onclick="window.print()">🖨️ Print Invoice</button>

        <div class="header">
            <h1>INVOICE</h1>
            <div class="company-info">
                <strong>Island Distribution Portal - <?php echo htmlspecialchars($province); ?> RDC</strong><br>
                Regional Distribution Center<br>
                Tel: +94 11 234 5678 | Email: orders@islanddistro.lk
            </div>
        </div>

        <div class="invoice-info">
            <div>
                <h3>Bill To:</h3>
                <p>
                    <strong><?php echo htmlspecialchars($order['business_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['full_name']); ?><br>
                    <?php echo htmlspecialchars($order['billing_address']); ?><br>
                    Phone: <?php echo htmlspecialchars($order['phone_number']); ?><br>
                    Email: <?php echo htmlspecialchars($order['email']); ?>
                </p>
            </div>
            <div>
                <h3>Ship To:</h3>
                <p>
                    <strong><?php echo htmlspecialchars($order['business_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                    <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_province']); ?>
                </p>
            </div>
        </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <td>Invoice Number:</td>
                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td>Invoice Date:</td>
                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                </tr>
                <tr>
                    <td>Order Status:</td>
                    <td>
                        <span class="status-badge status-<?php echo $order['order_status']; ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </td>
                    <td>Payment Status:</td>
                    <td>
                        <span class="status-badge status-<?php echo $order['payment_status'] === 'paid' ? 'delivered' : 'pending'; ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Description</th>
                    <th>SKU</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                foreach ($items as $item):
                ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['sku']); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">Rs. <?php echo number_format($item['unit_price'], 2); ?></td>
                        <td style="text-align: right;">Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>Rs. <?php echo number_format($order['total_amount'] - $order['shipping_fee'] + $order['discount_amount'], 2); ?></td>
                </tr>
                <?php if ($order['discount_amount'] > 0): ?>
                    <tr>
                        <td>Discount:</td>
                        <td>- Rs. <?php echo number_format($order['discount_amount'], 2); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($order['shipping_fee'] > 0): ?>
                    <tr>
                        <td>Shipping Fee:</td>
                        <td>Rs. <?php echo number_format($order['shipping_fee'], 2); ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="grand-total">
                    <td><strong>TOTAL:</strong></td>
                    <td><strong>Rs. <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>© <?php echo date('Y'); ?> Island Distribution Portal. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>