<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

$success = false;
$fine = null;

// Fetch case details by challan
if (isset($_GET['challan'])) {
    $challan_number = $_GET['challan'];

    $stmt = $conn->prepare("SELECT fines.*, users.username FROM fines LEFT JOIN users ON fines.user_id = users.id WHERE fines.challan_number = ?");
    $stmt->bind_param("s", $challan_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $fine = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $challan_number = $_POST['challan_number'];
    $vehicle_number = $_POST['vehicle_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $violation_type = $_POST['violation_type'];
    $violation_date = $_POST['violation_date'];

    $stmt = $conn->prepare("UPDATE fines SET vehicle_number = ?, vehicle_type = ?, violation_type = ?, violation_date = ? WHERE challan_number = ?");
    $stmt->bind_param("sssss", $vehicle_number, $vehicle_type, $violation_type, $violation_date, $challan_number);
    $success = $stmt->execute();
    $stmt->close();

    // Re-fetch updated fine
    $stmt = $conn->prepare("SELECT fines.*, users.username FROM fines LEFT JOIN users ON fines.user_id = users.id WHERE fines.challan_number = ?");
    $stmt->bind_param("s", $challan_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $fine = $result->fetch_assoc();
    $stmt->close();

    // Redirect with popup
    echo "<script>
        alert('✅ Fine edited successfully!');
        window.location.href = 'police_dashboard.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Fine - FinePay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #ece9e6, #ffffff);
      font-family: 'Segoe UI', sans-serif;
      padding: 50px 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .form-card {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 650px;
    }

    .form-card h3 {
      font-weight: bold;
      color: #333;
      text-align: center;
      margin-bottom: 30px;
    }

    .form-label {
      font-weight: 500;
    }

    .form-control, .form-select {
      border-radius: 10px;
      padding: 10px 15px;
    }

    .btn-primary {
      background-color: #2a5298;
      border: none;
      padding: 12px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #1e3c72;
    }

    .read-only {
      background-color: #f8f9fa;
      border: 1px solid #ced4da;
    }
  </style>
</head>
<body>

<div class="form-card">
  <h3>Edit Case Details</h3>

  <?php if ($fine): ?>
    <form method="POST">
      <input type="hidden" name="challan_number" value="<?= htmlspecialchars($fine['challan_number']) ?>">

      <div class="mb-3">
        <label class="form-label">Challan Number</label>
        <input type="text" class="form-control read-only" value="<?= htmlspecialchars($fine['challan_number']) ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control read-only" value="<?= htmlspecialchars($fine['username'] ?? 'N/A') ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">NIC</label>
        <input type="text" class="form-control read-only" value="<?= htmlspecialchars($fine['nic']) ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Vehicle Number</label>
        <input type="text" name="vehicle_number" class="form-control" value="<?= htmlspecialchars($fine['vehicle_number']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Vehicle Type</label>
        <select name="vehicle_type" class="form-select" required>
          <?php
          $vehicle_types = ["Bike", "Car", "Van", "Three Wheeler", "Bus", "Truck"];
          foreach ($vehicle_types as $vt) {
              $selected = ($fine['vehicle_type'] === $vt) ? "selected" : "";
              echo "<option value=\"$vt\" $selected>$vt</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Violation Type</label>
        <select name="violation_type" class="form-select" required>
          <?php
          $types = [
            "Expired Registration", "Driving without License", "Invalid Insurance",
            "Modified Vehicle without Approval", "Number-plate Violation", "Over Speeding",
            "Drunk Driving", "Using Mobile while Driving", "Driving without Helmet",
            "Signal Jumping", "Wrong Side Driving"
          ];
          foreach ($types as $type) {
              $selected = ($fine['violation_type'] === $type) ? "selected" : "";
              echo "<option value=\"$type\" $selected>$type</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Violation Date</label>
        <input type="date" name="violation_date" class="form-control" value="<?= htmlspecialchars($fine['violation_date']) ?>" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Save Changes</button>
    </form>
  <?php else: ?>
    <div class="alert alert-danger text-center">❌ Fine not found.</div>
  <?php endif; ?>
</div>

</body>
</html>
