<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finepay_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if challan number is set
if (isset($_GET['challan'])) {
    $challan = $conn->real_escape_string($_GET['challan']);

    // Optional: check if the challan exists before deleting
    $check = $conn->query("SELECT * FROM fines WHERE challan_number = '$challan'");
    if ($check && $check->num_rows > 0) {
        $delete = $conn->query("DELETE FROM fines WHERE challan_number = '$challan'");
        if ($delete) {
            $_SESSION['message'] = "Fine with Challan #$challan deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting record.";
        }
    } else {
        $_SESSION['error'] = "Challan number not found.";
    }
} else {
    $_SESSION['error'] = "No challan number provided.";
}

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit;
