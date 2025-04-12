<?php
include 'config.php';  // Include your config file

// Test database connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
} else {
    echo "✅ Database connection successful!<br>";
}

// Test Stripe keys
echo "STRIPE_SECRET_KEY: " . STRIPE_SECRET_KEY . "<br>";
echo "STRIPE_PUBLISHABLE_KEY: " . STRIPE_PUBLISHABLE_KEY;
?>
