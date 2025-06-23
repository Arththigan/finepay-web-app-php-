<?php
session_start();
$nic = '';
$license_no = '';
$username = '';

// Function to generate unique challan number
function generateUniqueChallanNumber($conn) {
    do {
        $challan_number = strval(mt_rand(100000000000, 999999999999)); // 12-digit number
       $stmt = $conn->prepare("SELECT challan_number FROM fines WHERE challan_number = ?");
        $stmt->bind_param("s", $challan_number);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $challan_number;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "finepay_db");

if (!$conn->connect_error) {
    if (isset($_GET['nic'])) {
        $nic_input = $_GET['nic'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE nic = ?");
        $stmt->bind_param("s", $nic_input);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            $nic = $user['nic'];
            $license_no = $user['license_no'];
            $username = $user['username'];
        }
        $stmt->close();
    }

    // Generate the unique challan number
    $challan_number = generateUniqueChallanNumber($conn);
    $conn->close();
} else {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Police Admin Panel - Register Fine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
        }
        .sidebar {
            height: 100vh;
            width: 220px;
            background-color: #24275e;
            padding-top: 30px;
            position: fixed;
            top: 0;
            left: 0;
            color: white;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 22px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #3e4196;
        }
        .content {
            margin-left: 220px;
            padding: 30px;
            width: calc(100% - 220px);
        }
        .challan-form-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 600;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Police Panel</h2>
    <a href="police_panel.php">➕ Dashboard</a>
    <a href="police_dashboard.php">📋 View All Cases</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
    <div class="challan-form-container">
        <h2>Challan Form</h2>
        <form action="submit_fine.php" method="POST">
            <div class="mb-3">
                <label for="challan_number" class="form-label">
                    Traffic Challan Number <small class="text-muted">(receipt or document issued by police)</small>
                </label>
                <input type="text" class="form-control" id="challan_number" name="challan_number" value="<?= htmlspecialchars($challan_number) ?>" readonly>
            </div>

           

            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" class="form-control" id="nic" name="nic" minlength="10" maxlength="12" placeholder="Enter NIC" value="<?= htmlspecialchars($nic) ?>">
            </div>

            <div class="mb-3">
                <label for="license_no" class="form-label">License Number</label>
                <input type="text" class="form-control" id="license_no" name="license_no" minlength="10" maxlength="20" placeholder="Enter License Number" value="<?= htmlspecialchars($license_no) ?>">
            </div>

            <div class="mb-3">
                <label for="vehicle_type" class="form-label">Vehicle Type</label>
                <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                    <option value="">-- Select Vehicle Type --</option>
                    <option value="Bike">Bike</option>
                    <option value="Car">Car</option>
                    <option value="Van">Van</option>
                    <option value="Three Wheeler">Three Wheeler</option>
                    <option value="Bus">Bus</option>
                    <option value="Truck">Truck</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="vehicle_number" class="form-label">Vehicle Number</label>
                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" minlength="7" maxlength="10" placeholder="Enter your vehicle number" required>
            </div>

            <div class="mb-3">
                <label for="violation_type" class="form-label">Violation Type</label>
                <select class="form-select" id="violation_type" name="violation_type" required>
                    <option value="">-- Select Violation Type --</option>
                    <option value="Expired Registration">Expired Registration</option>
                    <option value="Driving without License">Driving without License</option>
                    <option value="Invalid Insurance">Invalid Insurance</option>
                    <option value="Modified Vehicle without Approval">Modified Vehicle without Approval</option>
                    <option value="Number-plate Violation">Number-plate Violation</option>
                    <option value="Over Speeding">Over Speeding</option>
                    <option value="Drunk Driving">Drunk Driving</option>
                    <option value="Using Mobile while Driving">Using Mobile while Driving</option>
                    <option value="Driving without Helmet">Driving without Helmet</option>
                    <option value="Signal Jumping">Signal Jumping</option>
                    <option value="Wrong Side Driving">Wrong Side Driving</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="violation_date" class="form-label">Date of Violation</label>
                <input type="date" class="form-control" id="violation_date" name="violation_date" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register Fine</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
