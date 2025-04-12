<?php
require 'vendor/autoload.php';
require 'config.php';

// Initialize Stripe with your secret key
\Stripe\Stripe::setApiKey('sk_test_51QxK0V2MOcXousaRsVomfo8rd8gEeorVti5asKHCemjEmWWB7VQ10d9M4v8KQNM3hNVvzcljNrgQZ7tr947pZvdi00i9Abm3u4'); // Replace with your Stripe secret key

// Check if course_id is provided
if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    die("Error: No course selected.");
}

$course_id = (int)$_POST['course_id'];

// Fetch course details from the database
$stmt = $db->prepare("SELECT id, name, price FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    die("Error: Course not found.");
}

// Create a Stripe Checkout Session
try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $course['name'],
                ],
                'unit_amount' => $course['price'], // Price in cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/course_platform/success.php?session_id={CHECKOUT_SESSION_ID}', // Localhost for now
        'cancel_url' => 'http://localhost/course_platform/index.php', // Localhost for now
    ]);

    // Redirect to Stripe Checkout
    header("Location: " . $session->url);
    exit;
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Handle Stripe errors
    die("Error: " . $e->getMessage());
}
