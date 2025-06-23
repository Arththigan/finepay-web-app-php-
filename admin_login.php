<?php
session_start();
$error = "";

// Define allowed admin credentials
$admins = [
    "admin1" => "pass123",
    "admin2" => "secure456",
    "admin3" => "hello789",
    "admin4" => "alpha321",
    "admin5" => "bravo999"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (array_key_exists($username, $admins) && $admins[$username] === $password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid admin username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FinePay</title>
</head>
<body>
<style>
    body {
        height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: Arial, Helvetica, sans-serif;
        background-color: rgb(255, 255, 255);
    }

    .login-container {
        margin-top: 10px;
    }

    .login-box {
        background: #ffffff;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
        width: 400px;
        height: auto;
    }

    .finepay-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 40px;
        gap: 10px;
    }

    .finepay-logo {
        width: 60px;
    }

    .finepay-title {
        height: 70px;
    }

    .header img {
        display: block;
        margin: 0 auto 20px auto;
        width: 60%;
    }

    .input-group label {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .input-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 40px;
    }

    .login-btn {
        width: 90%;
        background-color: #005eff;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 20px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 20px;
    }

    .login-btn:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .error-message {
        color: red;
        text-align: center;
        margin-top: 10px;
    }
</style>

<div class="finepay-header">
    <img src="images/images/shield.png" alt="finepay-logo" class="finepay-logo">
    <img src="images/images/Fine Pay.png" alt="finepay-title" class="finepay-title">
</div>

<div class="login-container">
    <div class="login-box">
        <div class="header">
            <img src="images/images/SL-Police.png" alt="srilanka police" class="police-logo">
        </div>

        <form method="POST">
            <center><h2>Police Administration Login</h2></center>

            <div class="input-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" minlength="4" maxlength="15"  placeholder="Enter admin username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="4" maxlength="15" placeholder="Enter password" required>
            </div>

            <center><button type="submit" class="login-btn">Login</button></center>

            <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        </form>
    </div>
</div>
</body>
</html>
