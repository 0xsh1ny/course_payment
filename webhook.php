<?php
require 'vendor/autoload.php';
require 'config.php';

// Set your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51QxK0V2MOcXousaRsVomfo8rd8gEeorVti5asKHCemjEmWWB7VQ10d9M4v8KQNM3hNVvzcljNrgQZ7tr947pZvdi00i9Abm3u4'); // Replace with your Stripe test secret key

// Read the raw input
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    // Get webhook secret from Stripe CLI output (you'll set this later)
    $webhook_secret = 'whsec_d4160860b8e199ad485dd89b91bb1a36996897d424cb782523b44c5120115591'; // Replace with secret from `stripe listen`

    // Verify the webhook signature
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);

    // Handle the event
    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;

        // Ensure payment is successful
        if ($session->payment_status === 'paid') {
            $course_id = $session->metadata->course_id ?? null;
            $customer_email = $session->customer_details->email ?? 'unknown@example.com';
            $amount = $session->amount_total; // In cents
            $transaction_id = $session->payment_intent;

            // Store transaction in the database
            $stmt = $db->prepare("
                INSERT INTO transactions (course_id, customer_email, amount, transaction_id, status, created_at)
                VALUES (?, ?, ?, ?, 'completed', NOW())
            ");
            $stmt->bind_param("isds", $course_id, $customer_email, $amount, $transaction_id);
            $stmt->execute();
            $stmt->close();

            // Send email notifications
            sendPurchaseEmails($customer_email, $course_id, $amount, $transaction_id);
        }
    }

    // Respond with 200 OK
    http_response_code(200);
    echo "Webhook received";
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    echo "Webhook signature verification failed: " . $e->getMessage();
} catch (\Exception $e) {
    // General error
    http_response_code(400);
    echo "Error: " . $e->getMessage();
}

function sendPurchaseEmails($customer_email, $course_id, $amount, $transaction_id) {
    require 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mayuriiislam@gmail.com'; // Replace
        $mail->Password = 'enbz zdbh epmv znfx'; // Replace
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        global $db;
        $stmt = $db->prepare("SELECT name FROM courses WHERE id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();
        $stmt->close();
        $course_name = $course['name'] ?? 'Unknown Course';

        // Customer email
        $mail->setFrom('your-email@gmail.com', 'Course Platform');
        $mail->addAddress($customer_email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Course Purchase Confirmation';
        $mail->Body = "
            <h2>Thank You for Your Purchase!</h2>
            <p>You have successfully purchased <strong>$course_name</strong>.</p>
            <p>Amount: $" . number_format($amount / 100, 2) . "</p>
            <p>Transaction ID: $transaction_id</p>
            <p>You can now access your course in your account.</p>
        ";
        $mail->send();

        // Admin email
        $mail->clearAddresses();
        $mail->addAddress('mayuriiislam@gmail.com'); // Replace
        $mail->Subject = 'New Course Purchase Notification';
        $mail->Body = "
            <h2>New Purchase</h2>
            <p>Course: $course_name</p>
            <p>Customer Email: $customer_email</p>
            <p>Amount: $" . number_format($amount / 100, 2) . "</p>
            <p>Transaction ID: $transaction_id</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
    }
}
