<?php
session_start();

if (!isset($_SESSION['user_nic'])) {
    header("Location: index.php");
    exit();
}

$nic = $_SESSION['user_nic'];
$search_input = $_GET['search'] ?? '';

$conn = new mysqli("localhost", "root", "", "finepay_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM fines WHERE nic = ?";
$params = [$nic];
$types = "s";

if (!empty($search_input)) {
    $query .= " AND (nic = ? OR challan_number = ?)";
    $params[] = $search_input;
    $params[] = $search_input;
    $types .= "ss";
}

$stmt = $conn->prepare($query);
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}
call_user_func_array([$stmt, 'bind_param'], $bind_names);

$stmt->execute();
$result = $stmt->get_result();
$fines = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - FinePay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
      display: flex;
    }

    .sidebar {
      width: 240px;
      background: linear-gradient(to bottom, #24275e, #3e4196);
      height: 100vh;
      padding: 30px 20px;
      color: white;
      position: fixed;
    }

    .sidebar h2 {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 40px;
    }

    .sidebar a {
      display: block;
      color: white;
      padding: 12px 20px;
      margin-bottom: 12px;
      border-radius: 8px;
      text-decoration: none;
      transition: 0.3s;
    }

    .sidebar a:hover {
      background-color: #5558a4;
    }

    .main-content {
      margin-left: 240px;
      padding: 40px;
      width: calc(100% - 240px);
    }

    .form-control, .btn {
      border-radius: 8px;
    }

    .receipt-card {
      background: #fff;
      border-left: 5px solid #4c59c2;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      transition: transform 0.2s ease;
    }

    .receipt-card:hover {
      transform: translateY(-5px);
    }

    .receipt-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .receipt-title {
      font-size: 20px;
      font-weight: bold;
      color: #3e4196;
    }

    .receipt-details div {
      margin-bottom: 8px;
      font-size: 15px;
    }

    .receipt-details div span {
      font-weight: 600;
      color: #333;
    }

    .pay-btn {
      float: right;
      margin-top: 10px;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }

    .modal.show {
      display: block;
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 30px;
      border-radius: 15px;
      width: 400px;
      max-width: 90%;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.4s ease;
    }

    .close {
      float: right;
      font-size: 24px;
      color: #e74c3c;
      cursor: pointer;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    .logo { width: 150px; height: 70px; }
    .shield { height: 90px; }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="logo-title">
    <img src="images/images/shield.png" alt="shield" class="shield">
    <img src="images/images/Fine Pay(w).png" alt="Logo" class="logo">
  </div>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
  <h1 class="text-primary">My Dashboard</h1>
  <p class="mb-4">All your traffic fine records will be displayed below.</p>

  <?php if (isset($_SESSION['payment_error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['payment_error']; unset($_SESSION['payment_error']); ?></div>
  <?php endif; ?>

  <?php if (count($fines) > 0): ?>
    <?php foreach ($fines as $fine): ?>
      <div class="receipt-card">
        <div class="receipt-header">
          <div class="receipt-title">🚔 Fine Receipt - <?= htmlspecialchars($fine['challan_number']) ?></div>
        </div>
        <div class="receipt-details">
          <div><span>NIC:</span> <?= htmlspecialchars($fine['nic']) ?></div>
          <div><span>License No:</span> <?= htmlspecialchars($fine['license_no']) ?></div>
          <div><span>Vehicle Number:</span> <?= htmlspecialchars($fine['vehicle_number']) ?></div>
          <div><span>Vehicle Type:</span> <?= htmlspecialchars($fine['vehicle_type']) ?></div>
          <div><span>Violation:</span> <?= htmlspecialchars($fine['violation_type']) ?></div>
          <div><span>Date:</span> <?= htmlspecialchars($fine['violation_date']) ?></div>
          <div><span>Amount:</span> <span class="text-danger fw-bold">Rs. <?= number_format($fine['fine_amount'], 2) ?></span></div>
          <div><span>Status:</span> <span class="<?= $fine['status'] === 'paid' ? 'text-success' : 'text-warning' ?> fw-bold"><?= ucfirst($fine['status']) ?></span></div>
        </div>

        <?php if ($fine['status'] !== 'paid'): ?>
          <button class="btn btn-success pay-btn" onclick="openModal('<?= $fine['challan_number'] ?>')">💳 Pay Now</button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="alert alert-success">🎉 No fines found under your NIC.</div>
  <?php endif; ?>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h5 class="mb-3 text-center text-primary">Pay Your Fine</h5>
    <form action="make_payment.php" method="post">
      <input type="hidden" name="challan_number" id="modalChallanNumber">
      <div class="mb-3">
        <label>Card Holder Name</label>
        <input type="text" name="card_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Card Number</label>
        <input type="text" name="card_number" maxlength="12" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Card Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-success w-100">Confirm Payment</button>
    </form>
  </div>
</div>

<script>
function openModal(challanNumber) {
  document.getElementById('modalChallanNumber').value = challanNumber;
  document.getElementById('paymentModal').classList.add('show');
}

function closeModal() {
  document.getElementById('paymentModal').classList.remove('show');
}

window.onclick = function(event) {
  const modal = document.getElementById('paymentModal');
  if (event.target === modal) {
    closeModal();
  }
}
</script>

</body>
</html>
