<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a unique 12-digit challan number
function generateUniqueChallanNumber($conn) {
    do {
        $challan_number = strval(mt_rand(100000000000, 999999999999));
        $stmt = $conn->prepare("SELECT id FROM fines WHERE challan_number = ?");
        $stmt->bind_param("s", $challan_number);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $challan_number;
}

// Sanitize inputs
$nic = trim($_POST['nic']);
$license_no = trim($_POST['license_no']);
$vehicle_type = $_POST['vehicle_type'];
$vehicle_number = trim($_POST['vehicle_number']);
$violation_type = $_POST['violation_type'];
$violation_date = $_POST['violation_date'];
$fine_amount = 500.00; // default or fixed fine

// Fetch user_id by NIC
$user_id = null;
$stmt = $conn->prepare("SELECT id FROM users WHERE nic = ?");
$stmt->bind_param("s", $nic);
$stmt->execute();
$stmt->bind_result($uid);
if ($stmt->fetch()) {
    $user_id = $uid;
}
$stmt->close();

// Generate unique challan number
$challan_number = generateUniqueChallanNumber($conn);

// Insert new fine
$insert = $conn->prepare("INSERT INTO fines 
    (challan_number, nic, license_no, vehicle_type, vehicle_number, violation_type, violation_date, fine_amount, user_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$insert->bind_param("sssssssdi", $challan_number, $nic, $license_no, $vehicle_type, $vehicle_number, $violation_type, $violation_date, $fine_amount, $user_id);

if ($insert->execute()) {
    $message = "Fine registered successfully.\\nChallan Number: " . $challan_number;
} else {
    $message = "Failed to register fine. Please try again.";
}
$insert->close();
$conn->close();

// Show JavaScript alert and redirect
echo "<script>
    alert('$message');
    window.location.href = 'police_dashboard.php';
</script>";
exit();
?>
