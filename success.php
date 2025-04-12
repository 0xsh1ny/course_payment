<?php
require 'vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey('sk_test_51QxK0V2MOcXousaRsVomfo8rd8gEeorVti5asKHCemjEmWWB7VQ10d9M4v8KQNM3hNVvzcljNrgQZ7tr947pZvdi00i9Abm3u4'); // Same test key as checkout.php

if (!isset($_GET['session_id'])) {
    die("Error: No session ID provided.");
}

try {
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
    if ($session->payment_status === 'paid') {
        // Placeholder: Add logic later to grant course access (e.g., update database)
        echo "Payment successful! You now have access to the course.";
    } else {
        echo "Payment not completed.";
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
    die("Error: " . $e->getMessage());
}
