<?php
include 'db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nic = trim($_POST['user_nic']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE nic = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nic);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_nic'] = $user['nic']; // ✅ Session with NIC
                header("Location: user_dashboard.php");
                exit();
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "❌ NIC not found.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinePay.com</title>
    <link rel="stylesheet" href=#>

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
    ;
    
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
    width: 80px;
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
    margin-top: 20px;
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
    
</head>
<body>
   
    <div class="finepay-header">
        <img src="images/images/shield.png" alt="finepay-logo" class="finepay-logo">
        <img src="images/images/Fine Pay(w).png" alt="finepay-title" class="finepay-title">
    </div>

    <div class="login-container">
        <div class="login-box">
            <div class="header">
                <a href="police_login.php" class="admin">Police</a>
                <img src="images/images/SL-Police.png" alt="srilanka police" class="police-logo">
            </div>

            <form action="login.php" method="post">
                <div class="Login-account">
                    <center><h2>Login User Account</h2></center>
                </div>

                <div class="input-group">
               <label for="user_nic">NIC Number</label>
               <input type="text" id="user_nic" name="user_nic" minlength="10" maxlength="12" placeholder="Enter your NIC Number" required>
               </div>


                <div class="input-group">
                    <label for="username">User Name</label>
                    <input type="text" id="username" name="username" minlength="4" maxlength="15"   placeholder="Enter your User Name" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" minlength="4" maxlength="15"   placeholder="Enter your password" required>
                </div>

                <center><button type="submit" class="login-btn">Login</button></center>

                <p class="Signup-text">Don't have an account? <a href="create.php">Sign Up</a></p>

                <?php if (!empty($error)) { echo "<p style='color:red; text-align:center;'>$error</p>"; } ?>
            </form>


        </div>
    </div>    
</body>
</html>
