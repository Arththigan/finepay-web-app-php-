<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for payment success session data
if (!isset($_SESSION['payment_success']) || !isset($_SESSION['last_challan']) || !isset($_SESSION['user_nic'])) {
    header("Location: user_dashboard.php");
    exit();
}

$user_nic = $_SESSION['user_nic'];
$challan_number = $_SESSION['last_challan'];

// Fetch payment details for that challan & user
// First get user_id
$user_stmt = $conn->prepare("SELECT id FROM users WHERE nic = ?");
$user_stmt->bind_param("s", $user_nic);
$user_stmt->execute();
$user_res = $user_stmt->get_result();

if ($user_res->num_rows === 0) {
    $_SESSION['payment_error'] = "User not found.";
    header("Location: user_dashboard.php");
    exit();
}

$user_id = $user_res->fetch_assoc()['id'];
$user_stmt->close();

// Now get payment details
$pay_stmt = $conn->prepare("SELECT card_name, card_number, payment_date FROM payments WHERE user_id = ? AND challan_number = ? ORDER BY payment_date DESC LIMIT 1");
$pay_stmt->bind_param("is", $user_id, $challan_number);
$pay_stmt->execute();
$pay_res = $pay_stmt->get_result();

if ($pay_res->num_rows === 0) {
    $_SESSION['payment_error'] = "Payment record not found.";
    header("Location: user_dashboard.php");
    exit();
}

$payment = $pay_res->fetch_assoc();
$pay_stmt->close();

// Clear payment success session to avoid reuse
unset($_SESSION['payment_success']);
unset($_SESSION['last_challan']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Payment Receipt - FinePay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
        }
        .receipt-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .receipt-container h1 {
            color: #3e4196;
            margin-bottom: 20px;
        }
        .receipt-details {
            text-align: left;
            margin-top: 20px;
        }
        .receipt-details dt {
            font-weight: 600;
        }
        .btn-home {
            margin-top: 30px;
            background-color: #3e4196;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-home:hover {
            background-color: #5558a4;
            color: white;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <h1>Payment Successful! 🎉</h1>
    <p>Your payment for Challan Number <strong><?= htmlspecialchars($challan_number) ?></strong> has been received.</p>

    <dl class="receipt-details">
        <dt>Card Holder Name:</dt>
        <dd><?= htmlspecialchars($payment['card_name']) ?></dd>

        <dt>Card Number:</dt>
        <dd><?= htmlspecialchars($payment['card_number']) ?></dd>

        <dt>Payment Date:</dt>
        <dd><?= htmlspecialchars(date("F j, Y, g:i a", strtotime($payment['payment_date']))) ?></dd>
    </dl>

    <a href="user_dashboard.php" class="btn-home">Go Back to Dashboard</a>
</div>

</body>
</html>
