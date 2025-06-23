<?php
include 'db.php';

// ✅ Proper session start check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM police WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['officer_id'] = $user['officer_id'];
                header("Location: police_dashboard.php");
                exit();
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "❌ Police account not found.";
        }
    } else {
        $error = "❌ Database error.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FinePay.com - Police Login</title>
    <link rel="stylesheet" href="CSS/finepay_login.css">
    <style>
        /* Styling same as your current file */
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: Arial, Helvetica, sans-serif;
            background-color: rgb(5, 12, 146);
        }
        .login-container { margin-top: 10px; }
        .login-box {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
            width: 400px;
        }
        .finepay-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 40px;
            gap: 10px;
        }
        .finepay-logo { width: 60px; height: auto; }
        .finepay-title { height: 70px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .police-logo {
            display: block;
            margin: 0 auto 20px auto;
            width: 60%;
            padding-right: 50px;
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
        .signup-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .signup-text a {
            color:#005eff;
           
        }
        .error-msg {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="finepay-header">
        <img src="images/images/shield.png" alt="finepay-logo" class="finepay-logo">
        <img src="images/images/Fine Pay(w).png" alt="finepay-title" class="finepay-title">
    </div>

    <div class="login-container">
        <div class="login-box">
            <div class="header">
                <a href="login.php" class="admin">User</a>
                <img src="images/images/SL-Police.png" alt="srilanka police" class="police-logo">
            </div>

            <form method="post" action="">
                <center><h2>Login Police Account</h2></center>

                <div class="input-group">
                    <label for="username">User Name</label>
                    <input type="text" id="username" name="username" placeholder="Enter your User Name" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <center><button type="submit" class="login-btn">Login</button></center>

                <p class="signup-text">Don't have an account? <a href="police_create.php">Sign Up</a></p>
            </form>

            <?php if (!empty($error)): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
