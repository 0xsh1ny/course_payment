<?php
$db = new mysqli('localhost', 'root', '', 'course_platform');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
define('STRIPE_SECRET_KEY', 'your_secret_key_here');
define('STRIPE_PUBLISHABLE_KEY', 'your_publishable_key_here');
?>
