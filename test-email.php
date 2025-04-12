<?php
require 'vendor/autoload.php';
$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mayuriiislam@gmail.com'; // Your Gmail
    $mail->Password = 'enbz zdbh epmv znfx'; // Your app password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('your.email@gmail.com', 'Test');
    $mail->addAddress('gbxxislam@gmail.com'); // Your email
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = '<p>This is a test email from PHPMailer.</p>';
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Email sending failed: {$mail->ErrorInfo}";
}
