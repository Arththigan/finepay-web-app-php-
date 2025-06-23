<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_nic'])) {
    $_SESSION['payment_error'] = "User not logged in.";
    header("Location: user_dashboard.php");
    exit();
}

$user_nic = $_SESSION['user_nic'];
$challan_number = $_POST['challan_number'] ?? '';
$card_name = trim($_POST['card_name'] ?? '');
$card_number = trim($_POST['card_number'] ?? '');
$password = trim($_POST['password'] ?? ''); // Not stored

if (empty($challan_number) || empty($card_name) || empty($card_number) || empty($password)) {
    $_SESSION['payment_error'] = "All payment fields are required.";
    header("Location: user_dashboard.php");
    exit();
}

// Hardcoded valid card for demo
$valid_card_name = "John Doe";
$valid_card_number = "123456789012";
$valid_password = "1234";

// Validate card
$is_valid_card = ($card_name === $valid_card_name && $card_number === $valid_card_number && $password === $valid_password);

// If not valid, check for duplicate card
if (!$is_valid_card) {
    $dup_stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM payments WHERE card_name = ? AND card_number = ?");
    $dup_stmt->bind_param("ss", $card_name, $card_number);
    $dup_stmt->execute();
    $dup_result = $dup_stmt->get_result()->fetch_assoc();
    $dup_stmt->close();

    if ($dup_result['cnt'] > 0) {
        $is_valid_card = true; // Allow duplicate
    }
}

if (!$is_valid_card) {
    $_SESSION['payment_error'] = "Invalid card details.";
    header("Location: user_dashboard.php");
    exit();
}

// Get user ID
$user_query = $conn->prepare("SELECT id FROM users WHERE nic = ?");
$user_query->bind_param("s", $user_nic);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    $_SESSION['payment_error'] = "User not found.";
    header("Location: user_dashboard.php");
    exit();
}

$user_id = $user_result->fetch_assoc()['id'];
$user_query->close();

// Insert payment
$insert = $conn->prepare("INSERT INTO payments (user_id, challan_number, card_name, card_number, payment_date) VALUES (?, ?, ?, ?, NOW())");
$insert->bind_param("isss", $user_id, $challan_number, $card_name, $card_number);

if ($insert->execute()) {
    // ✅ Update fines table to mark as paid
    $update = $conn->prepare("UPDATE fines SET status = 'paid' WHERE challan_number = ?");
    $update->bind_param("s", $challan_number);
    $update->execute();
    $update->close();

    $_SESSION['payment_success'] = "Payment successful!";
    $_SESSION['last_challan'] = $challan_number;
    header("Location: receipt.php");
    exit();
} else {
    $_SESSION['payment_error'] = "Payment failed: " . $conn->error;
    header("Location: user_dashboard.php");
    exit();
}
?>
