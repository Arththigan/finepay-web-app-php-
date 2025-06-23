<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

// Count totals
$total_cases = $conn->query("SELECT COUNT(*) as total FROM fines")->fetch_assoc()['total'];
$paid_cases = $conn->query("SELECT COUNT(*) as paid FROM fines WHERE status = 'paid'")->fetch_assoc()['paid'];
$unpaid_cases = $conn->query("SELECT COUNT(*) as unpaid FROM fines WHERE status = 'unpaid'")->fetch_assoc()['unpaid'];

// Handle search
$search_input = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

$query = "
    SELECT fines.*, users.username 
    FROM fines 
    LEFT JOIN users ON fines.user_id = users.id
";

$conditions = [];

if (!empty(trim($search_input))) {
    $safe_search = $conn->real_escape_string($search_input);
    $conditions[] = "(fines.nic = '$safe_search' OR fines.challan_number = '$safe_search')";
}

if (!empty(trim($status_filter))) {
    $safe_status = $conn->real_escape_string($status_filter);
    $conditions[] = "fines.status = '$safe_status'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY fines.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
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

    .logo {
        width: 150px;
        height: 60px;
    }

    .shield {
        height: 60px;
    }
  </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-title">
        <img src="images/images/shield.png" alt="shield" class="shield">
        <img src="images/images/Fine Pay(w).png" alt="Logo" class="logo">
    </div>

    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">🚪 Dashboard</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h1 style="color:rgb(181, 7, 7);">Admin Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Cases</h5>
                    <p class="card-text fs-4"><?= $total_cases ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Paid Cases</h5>
                    <p class="card-text fs-4"><?= $paid_cases ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Unpaid Cases</h5>
                    <p class="card-text fs-4"><?= $unpaid_cases ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-4">Search Case Records</h4>
    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by NIC or Challan Number" value="<?= htmlspecialchars($search_input) ?>">
        </div>
        <div class="col-md-3">
            <select name="status_filter" class="form-select">
                <option value="">-- All Status --</option>
                <option value="paid" <?= $status_filter === 'paid' ? 'selected' : '' ?>> Paid cases</option>
                <option value="unpaid" <?= $status_filter === 'unpaid' ? 'selected' : '' ?>> Unpaid cases</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <div class="table-container">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Challan ID</th>
                    <th>Username</th>
                    <th>NIC</th>
                    <th>License No</th>
                    <th>Vehicle No</th>
                    <th>Vehicle Type</th>
                    <th>Violation</th>
                    <th>Date</th>
                    <th>Fine</th>
                    <th>Status</th>
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
                        <?php if ($row['status'] === 'paid'): ?>
                            <span class="badge bg-success">Paid</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="admin_edit_case.php?challan=<?= urlencode($row['challan_number']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_case.php?challan=<?= urlencode($row['challan_number']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this fine?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if ($result->num_rows == 0): ?>
                <tr><td colspan="11" class="text-center text-muted">No cases found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
