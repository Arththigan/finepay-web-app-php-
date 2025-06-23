<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$successMsg = $errorMsg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nic = trim($_POST['nic']);
    $license_no = trim($_POST['license_no']);

    if (!empty($username) && !empty($email) && !empty($_POST['password']) && !empty($nic) && !empty($license_no)) {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errorMsg = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, nic, license_no) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password, $nic, $license_no);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                echo "<script>alert('Account created successfully!'); window.location.href = 'login.php';</script>";
                exit();
            } else {
                $errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
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
    <title>FinePay.com</title>
    <link rel="stylesheet" href="#">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
                <a href="police_create.php" class="admin">Police</a>
                <img src="images/images/SL-Police.png" alt="srilanka police" class="police-logo">
            </div>

            <form method="post">
                <div class="Login-account">
                    <center><h2>Create User Account</h2></center>
                </div>

                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" minlength="5" maxlength="15"   placeholder="Enter your new username" required>
                </div>
                <div class="input-group">
                    <label for="nic">NIC</label>
                    <input type="text" id="nic" name="nic" minlength="10" maxlength="20" placeholder="Enter your NIC" required>
                </div>

                <div class="input-group">
                    <label for="license_no">License Number</label>
                    <input type="text" id="license_no" name="license_no" minlength="10" maxlength="20" placeholder="Enter your License Number" required>
                </div>


                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" minlength="10" maxlength="50"   placeholder="Enter your email address" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" minlength="4" maxlength="15"  placeholder="Enter your new password" required>
                </div>

                <div class="options">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                </div>

                <center><button type="submit" class="login-btn">Create Account</button></center>

                <p class="Signup-text">Already have an account? <a href="login.php">Sign in</a></p>
            </form>

            <?php if (!empty($errorMsg)): ?>
                <p style="color:red; text-align:center;"><?php echo $errorMsg; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>        
