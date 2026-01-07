<?php
// Stripe Configuration
// Add your Stripe API keys here

// Test Mode Keys (Get from: https://dashboard.stripe.com/test/apikeys)
define('STRIPE_SECRET_KEY', 'sk_test_51SJ5ck7xTxyRTXVzY5J9qWO6qr1LiTop17sdzgalYiHMIwcHtZtJy1siEhWjujMZUcy988PQH82XtweJcHsaWdm9002i4VBoGa'); // Replace with your test secret key
define('STRIPE_PUBLIC_KEY', 'pk_test_51SJ5ck7xTxyRTXVzDNdEZbPSSRJ4B2biFJamaoHXOV8ong5NQTFSpCUnijmdubN41dQG7wKXtX3TaKbMA7sG1mNL008CSboVxr'); // Replace with your test publishable key

// Live Mode Keys (Only use in production)
// define('STRIPE_SECRET_KEY', 'sk_live_YOUR_SECRET_KEY_HERE');
// define('STRIPE_PUBLIC_KEY', 'pk_live_YOUR_PUBLIC_KEY_HERE');

// Stripe Webhook Secret (for handling webhooks)
define('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET_HERE');

// Currency
define('CURRENCY', 'LKR'); // Sri Lankan Rupees

// Success and Cancel URLs
define('PAYMENT_SUCCESS_URL', 'http://localhost/Logis/public/Customer/Orders/order_success.php');
define('PAYMENT_CANCEL_URL', 'http://localhost/Logis/public/Customer/Cart/cart.php');
