<?php
include 'db.php';
session_start();

$success = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM police WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errorMsg = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO police (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                echo "<script>alert('Account created successfully!'); window.location.href = 'police_dashboard.php';</script>";
                exit();
            } else {
                $errorMsg = "Error: " . $stmt->error;
            }
        }

        $check->close();
    } else {
        $errorMsg = "All fields are required.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinePay.com</title>
    <link rel="stylesheet" href="CSS/finepay-createact.css">
</head>
<body>
    <div class="finepay-header">
        <img src="images/images/shield.png" alt="finepay-logo" class="finepay-logo">
        <img src="images/images/Fine Pay(w).png" alt="finepay-title" class="finepay-title">
    </div>

    <style>
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



.login-container {
   margin-top: 10px;
   
 
}

.login-box {

    background: #ffffff;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.6);
    width: 400px;
    
}

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

.admin{
    margin-bottom: 100px;
    
}
.title {
    display: flex;
    align-items: center;
    
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
    height: auto;
    
    
}

.finepay-title {
    height: 70px;
    
    
    
}

.input-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
      border: 1px;
      border-radius: 50px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 40px;
}

.options {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    margin-bottom: 20px;
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
}

.login-btn:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.google-signin {
    width: 90%;
    margin: 20px ;
    text-align: center;

}

.google-btn {
    display: flex;
    align-items: center;
    justify-content: start;
    gap: 10px;
    width: 100%;
    height: 40px;
    background: #f2f2f2;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
}

.google-logo {
    width: 20px;
}
.signup-text {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
    display: flex;
}

.signup-text a {
    color: #005eff;
    text-decoration: none;
}
    </style>

    <div class="login-container">
        <div class="login-box">
            <div class="header">
                <a href="login.php" class="admin">User</a>
                <img src="images/images/SL-Police.png" alt="srilanka police" class="police-logo">
            </div>

            <form action="" method="post">
                <div class="Login-account">
                    <center><h2>Create Police Account</h2></center>
                </div>

                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your new username" required>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Enter your email address" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your new password" required>
                </div>

                <div class="options">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                </div>

                <center><button type="submit" class="login-btn">Create account</button></center>

                <p class="Signup-text">Already have an account? <a href="police_login.php">Sign in</a></p>
            </form>

            <?php
include 'db.php';

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM police WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errorMsg = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO police (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                header("Location: index.php");
                exit();
            } else {
                $errorMsg = "Error: " . $stmt->error;
            }
        }

        $check->close();
    } else {
        $errorMsg = "All fields are required.";
    }

    $conn->close();
}
?>

        </div>
    </div>
</body>
</html>   


<!-- HTML FORM CODE STARTS BELOW -->
<!-- Add your full HTML code here (the form you already shared) -->
