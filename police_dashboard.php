<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$conn = new mysqli("localhost", "root", "", "finepay_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search_input = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search_input)) {
    $stmt = $conn->prepare("
        SELECT fines.*, users.username 
        FROM fines 
        LEFT JOIN users ON fines.user_id = users.id 
        WHERE fines.nic = ? OR fines.challan_number = ?
        ORDER BY fines.id DESC
    ");
    $stmt->bind_param("ss", $search_input, $search_input);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT fines.*, users.username 
        FROM fines 
        LEFT JOIN users ON fines.user_id = users.id 
        ORDER BY fines.id DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Police Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {margin: 0; font-family: 'Segoe UI', sans-serif; background: #f4f6f9; display: flex;}
    .sidebar {width: 240px; height: 100vh; background: linear-gradient(#24275e, #3e4196); color: white; padding: 30px 20px; position: fixed;}
    .sidebar h2 {text-align: center; font-size: 24px; margin-bottom: 30px;}
    .sidebar a {display: block; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; margin-bottom: 10px;}
    .sidebar a:hover {background-color: #5558a4;}
    .main {margin-left: 240px; padding: 40px; width: calc(100% - 240px);}
    .table-container {background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
    .sidebar .logo{width: 150px; height: 60px;}
    .shield{height: 60px;}
  </style>
  <script>
    window.onpageshow = function(event) {
      if (event.persisted) {
        window.location.reload();
      }
    };
  </script>
</head>
<body>

<div class="sidebar">
  <div class="logo-title">
      <img src="images/images/shield.png" alt="shield" class="shield">
      <img src="images/images/Fine Pay(w).png" alt="Logo" class="logo">
      <h2>Police Panel</h2>
  </div>

  <a href="fine_form.php">➕ Register Fine</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
  <h1 style="color: #3e4196;">Police Dashboard</h1>
  
  <h4 class="mb-4">Search Case Records</h4>
  <form method="get" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="search" class="form-control" placeholder="Search by NIC or Challan Number" value="<?= htmlspecialchars($search_input) ?>">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
  </form>

  <div class="table-container">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Challan #</th>
          <th>Username</th>
          <th>NIC</th>
          <th>License No</th>
          <th>Vehicle No</th>
          <th>Vehicle Type</th>
          <th>Violation</th>
          <th>Date</th>
          <th>Fine</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['challan_number']) ?></td>
            <td><?= htmlspecialchars($row['username'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['nic']) ?></td>
            <td><?= htmlspecialchars($row['license_no']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
            <td><?= htmlspecialchars($row['vehicle_type']) ?></td>
            <td><?= htmlspecialchars($row['violation_type']) ?></td>
            <td><?= htmlspecialchars($row['violation_date']) ?></td>
            <td>Rs. <?= number_format($row['fine_amount'], 2) ?></td>
            <td>
              <a href="edit_case.php?challan=<?= urlencode($row['challan_number']) ?>" class="btn btn-sm btn-warning">Edit</a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center text-muted">No cases found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
