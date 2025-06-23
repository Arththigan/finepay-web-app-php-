<?php
include 'db.php'; // Adjust this to your DB connection file

if (isset($_GET['challan_number'])) {
    $challan_number = $_GET['challan_number'];
    $stmt = $conn->prepare("SELECT * FROM fines WHERE challan_number = ?");
    $stmt->bind_param("s", $challan_number);
    $stmt->execute();
    $result = $stmt->get_result();

    include 'header.php'; // Optional: include Bootstrap-styled header

    echo '<div class="container mt-4">';

    if ($row = $result->fetch_assoc()) {
        echo '
        <div class="card shadow rounded">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Case Details - Challan #: ' . htmlspecialchars($row['challan_number']) . '</h5>
          </div>
          <div class="card-body">
            <p><strong>NIC:</strong> ' . htmlspecialchars($row['nic']) . '</p>
            <p><strong>License No:</strong> ' . htmlspecialchars($row['license_no']) . '</p>
            <p><strong>Vehicle Type:</strong> ' . htmlspecialchars($row['vehicle_type']) . '</p>
            <p><strong>Vehicle Number:</strong> ' . htmlspecialchars($row['vehicle_number']) . '</p>
            <p><strong>Violation Type:</strong> ' . htmlspecialchars($row['violation_type']) . '</p>
            <p><strong>Violation Date:</strong> ' . htmlspecialchars($row['violation_date']) . '</p>
            <p><strong>Created At:</strong> ' . htmlspecialchars($row['created_at']) . '</p>
          </div>
        </div>';
    } else {
        echo '<div class="alert alert-warning">No case found with Challan Number: <strong>' . htmlspecialchars($challan_number) . '</strong></div>';
    }

    echo '</div>';

    include 'footer.php'; // Optional: include footer
}
?>
