<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact_us.php");
    exit();
}

// Simple sanitization
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    $_SESSION['contact_error'] = "All fields are required.";
    header("Location: contact_us.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = "Invalid email address.";
    header("Location: contact_us.php");
    exit();
}

// Prepare email to admin
$to = "admin@example.com";  // Replace with your admin email
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$email_subject = "[FinePay Contact] " . $subject;

$email_body = "You have received a new message from the FinePay contact form.\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n\n";
$email_body .= "Message:\n$message\n";

if (mail($to, $email_subject, $email_body, $headers)) {
    $_SESSION['contact_success'] = "Thank you, your message has been sent successfully.";
} else {
    $_SESSION['contact_error'] = "Oops! Something went wrong. Please try again later.";
}

header("Location: contact_us.php");
exit();
