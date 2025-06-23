<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

// Handle search
$search_input = $_GET['search'] ?? '';
$query = "
    SELECT fines.*, users.username 
    FROM fines 
    LEFT JOIN users ON fines.user_id = users.id
";

if (!empty($search_input)) {
    $search_input = $conn->real_escape_string($search_input);
    $query .= " WHERE fines.nic = '$search_input' OR fines.challan_number = '$search_input'";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - FinePay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f4f6f9;
        display: flex;
    }

    .sidebar {
        width: 240px;
        height: 100vh;
        background: linear-gradient(to bottom, #1e1e2f, #2a5298);
        color: white;
        position: fixed;
        padding: 30px 20px;
    }

    .sidebar h2 {
        font-size: 24px;
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        margin-bottom: 10px;
        border-radius: 8px;
        transition: background 0.2s;
    }

    .sidebar a:hover {
        background-color: #3b5998;
    }

    .main {
        margin-left: 240px;
        padding: 40px;
        width: calc(100% - 240px);
    }

    .table-container {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-sm {
        margin-right: 5px;
    }

    .table th {
        background-color: #e9ecff;
        font-weight: 600;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="admin_dashboard.php">📋 Dashboard</a>
  <a href="view_all_cases.php">📁 View All Cases</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
  <h2 class="mb-4">Search Case Records</h2>

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
      <thead>
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
            <a href="admin_edit_case.php?challan=<?= urlencode($row['challan_number']) ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_case.php?challan=<?= urlencode($row['challan_number']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this fine?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows == 0): ?>
        <tr><td colspan="10" class="text-center text-muted">No cases found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
