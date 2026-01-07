<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart & Checkout Setup Checker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .check-item {
            background: #f9f9f9;
            border-left: 4px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .check-item.success {
            border-left-color: #22c55e;
            background: #f0fdf4;
        }

        .check-item.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }

        .check-item.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }

        .check-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .check-message {
            font-size: 14px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .status-badge.success {
            background: #22c55e;
            color: white;
        }

        .status-badge.error {
            background: #ef4444;
            color: white;
        }

        .status-badge.warning {
            background: #f59e0b;
            color: white;
        }

        code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 13px;
        }

        .action-required {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }

        .action-required h3 {
            color: #ea580c;
            margin-bottom: 10px;
        }

        .action-required ul {
            margin-left: 20px;
        }

        .action-required li {
            margin-bottom: 8px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🛒 Cart & Checkout Setup Checker</h1>
        <p class="subtitle">Verify all components are properly configured</p>

        <?php
        $checks = [];
        $errors = 0;
        $warnings = 0;

        // Check 1: Database connection
        try {
            require_once '../../../config/database.php';
            $checks[] = [
                'status' => 'success',
                'title' => 'Database Connection',
                'message' => 'Successfully connected to database'
            ];
        } catch (Exception $e) {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Database Connection',
                'message' => 'Failed to connect: ' . $e->getMessage()
            ];
        }

        // Check 2: Cart table exists
        if (isset($conn)) {
            $result = $conn->query("SHOW TABLES LIKE 'cart'");
            if ($result && $result->num_rows > 0) {
                $checks[] = [
                    'status' => 'success',
                    'title' => 'Cart Table',
                    'message' => 'Cart table exists in database'
                ];
            } else {
                $errors++;
                $checks[] = [
                    'status' => 'error',
                    'title' => 'Cart Table',
                    'message' => 'Cart table not found. Run: mysql -u root logis_db < db/cart_orders_schema.sql'
                ];
            }

            // Check 3: Orders table exists
            $result = $conn->query("SHOW TABLES LIKE 'orders'");
            if ($result && $result->num_rows > 0) {
                $checks[] = [
                    'status' => 'success',
                    'title' => 'Orders Table',
                    'message' => 'Orders table exists in database'
                ];
            } else {
                $errors++;
                $checks[] = [
                    'status' => 'error',
                    'title' => 'Orders Table',
                    'message' => 'Orders table not found. Run: mysql -u root logis_db < db/cart_orders_schema.sql'
                ];
            }

            // Check 4: Order Items table exists
            $result = $conn->query("SHOW TABLES LIKE 'order_items'");
            if ($result && $result->num_rows > 0) {
                $checks[] = [
                    'status' => 'success',
                    'title' => 'Order Items Table',
                    'message' => 'Order items table exists in database'
                ];
            } else {
                $errors++;
                $checks[] = [
                    'status' => 'error',
                    'title' => 'Order Items Table',
                    'message' => 'Order items table not found. Import cart_orders_schema.sql'
                ];
            }
        }

        // Check 5: Stripe config file exists
        if (file_exists('../../../config/stripe_config.php')) {
            require_once '../../../config/stripe_config.php';

            if (defined('STRIPE_SECRET_KEY') && STRIPE_SECRET_KEY !== 'sk_test_YOUR_SECRET_KEY_HERE') {
                $checks[] = [
                    'status' => 'success',
                    'title' => 'Stripe Configuration',
                    'message' => 'Stripe API keys are configured'
                ];
            } else {
                $warnings++;
                $checks[] = [
                    'status' => 'warning',
                    'title' => 'Stripe Configuration',
                    'message' => 'Stripe config exists but API keys are not set. Update config/stripe_config.php'
                ];
            }
        } else {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Stripe Configuration',
                'message' => 'stripe_config.php not found in config directory'
            ];
        }

        // Check 6: Stripe PHP library
        if (file_exists('../../../vendor/autoload.php')) {
            $checks[] = [
                'status' => 'success',
                'title' => 'Stripe PHP Library',
                'message' => 'Stripe SDK is installed via Composer'
            ];
        } else {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Stripe PHP Library',
                'message' => 'Stripe SDK not found. Run: composer require stripe/stripe-php'
            ];
        }

        // Check 7: Cart handler file
        if (file_exists('cart_handler.php')) {
            $checks[] = [
                'status' => 'success',
                'title' => 'Cart Handler',
                'message' => 'cart_handler.php exists'
            ];
        } else {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Cart Handler',
                'message' => 'cart_handler.php not found in Cart directory'
            ];
        }

        // Check 8: Checkout file
        if (file_exists('checkout.php')) {
            $checks[] = [
                'status' => 'success',
                'title' => 'Checkout Page',
                'message' => 'checkout.php exists'
            ];
        } else {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Checkout Page',
                'message' => 'checkout.php not found in Cart directory'
            ];
        }

        // Check 9: Order success handler
        if (file_exists('../Orders/order_success.php')) {
            $checks[] = [
                'status' => 'success',
                'title' => 'Order Success Handler',
                'message' => 'order_success.php exists'
            ];
        } else {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Order Success Handler',
                'message' => 'order_success.php not found in Orders directory'
            ];
        }

        // Check 10: JavaScript files
        if (file_exists('js/script.js')) {
            $js_content = file_get_contents('js/script.js');
            if (strpos($js_content, 'updateQuantity') !== false && strpos($js_content, 'removeFromCart') !== false) {
                $checks[] = [
                    'status' => 'success',
                    'title' => 'Cart JavaScript',
                    'message' => 'Cart management functions are implemented'
                ];
            } else {
                $warnings++;
                $checks[] = [
                    'status' => 'warning',
                    'title' => 'Cart JavaScript',
                    'message' => 'Cart functions may be incomplete'
                ];
            }
        } else {
            $errors++;
            $checks[] = [
                'status' => 'error',
                'title' => 'Cart JavaScript',
                'message' => 'js/script.js not found'
            ];
        }

        // Display all checks
        foreach ($checks as $check) {
            $class = $check['status'];
            $badge = strtoupper($check['status']);
            echo "<div class='check-item {$class}'>";
            echo "<div class='check-title'>{$check['title']} <span class='status-badge {$class}'>{$badge}</span></div>";
            echo "<div class='check-message'>{$check['message']}</div>";
            echo "</div>";
        }

        // Summary
        echo "<div style='margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px;'>";
        echo "<h3 style='margin-bottom: 10px;'>Summary</h3>";
        echo "<p><strong>Total Checks:</strong> " . count($checks) . "</p>";
        echo "<p><strong>Errors:</strong> <span style='color: #ef4444;'>{$errors}</span></p>";
        echo "<p><strong>Warnings:</strong> <span style='color: #f59e0b;'>{$warnings}</span></p>";
        echo "<p><strong>Passed:</strong> <span style='color: #22c55e;'>" . (count($checks) - $errors - $warnings) . "</span></p>";
        echo "</div>";

        // Action required section
        if ($errors > 0 || $warnings > 0) {
            echo "<div class='action-required'>";
            echo "<h3>⚠️ Action Required</h3>";
            echo "<ul>";

            if ($errors > 0) {
                echo "<li>Fix all <strong>ERROR</strong> items above before testing</li>";
            }

            if ($warnings > 0) {
                echo "<li>Address <strong>WARNING</strong> items for full functionality</li>";
            }

            echo "<li>See <code>README_CART_CHECKOUT.md</code> for detailed setup instructions</li>";
            echo "<li>Test the complete flow after fixing all issues</li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div style='background: #f0fdf4; border: 1px solid #86efac; padding: 15px; border-radius: 6px; margin-top: 20px;'>";
            echo "<h3 style='color: #16a34a; margin-bottom: 10px;'>✅ All Checks Passed!</h3>";
            echo "<p style='color: #166534;'>Your cart and checkout system is ready for testing.</p>";
            echo "<p style='margin-top: 10px;'><a href='../Catalog/catalog.php' style='color: #16a34a; font-weight: 600;'>Go to Catalog →</a></p>";
            echo "</div>";
        }
        ?>
    </div>
</body>

</html>