<?php
session_start();
if (!isset($_SESSION['payment_success']) || !isset($_SESSION['challan_number'])) {
    header("Location: receipt.php");
    exit();
}
$challan_number = $_SESSION['challan_number'];
unset($_SESSION['payment_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Success</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f3f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .popup {
      background: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      text-align: center;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }

    .popup h2 {
      color: green;
      margin-bottom: 20px;
    }

    .popup p {
      font-size: 18px;
      margin-bottom: 10px;
    }
  </style>
  <script>
    setTimeout(function () {
      window.location.href = 'receipt.php?challan=<?= urlencode($challan_number) ?>';
    }, 3000); // 3 seconds then go to receipt
  </script>
</head>
<body>
  <div class="popup">
    <h2>✅ Payment Successful!</h2>
    <p>Thank you for your payment.</p>
    <p>You will be redirected to your receipt shortly...</p>
  </div>
</body>
</html>
