<?php
session_start();
include 'db.php';

if (!isset($_SESSION['license_or_nic'])) {
    echo "Please log in to view your fine details.";
    exit();
}

$licenseOrNic = $_SESSION['license_or_nic'];

$sql = "SELECT * FROM case_details WHERE license_or_nic = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $licenseOrNic);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Fine Details</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .fine-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .fine-box h2 {
            text-align: center;
            color: #24275e;
        }
        .fine-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .fine-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="fine-box">
    <h2>Your Case Details</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='fine-item'>";
            echo "<strong>License/NIC:</strong> " . htmlspecialchars($row['license_or_nic']) . "<br>";
            echo "<strong>Vehicle Type:</strong> " . htmlspecialchars($row['vehicle_type']) . "<br>";
            echo "<strong>Vehicle Number:</strong> " . htmlspecialchars($row['vehicle_number']) . "<br>";
            echo "<strong>Violation Type:</strong> " . htmlspecialchars($row['violation_type']) . "<br>";
            echo "<strong>Violation Date:</strong> " . htmlspecialchars($row['violation_date']) . "<br>";
            echo "</div>";
        }
    } else {
        echo "<p>No fine records found under your account.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

</body>
</html>
