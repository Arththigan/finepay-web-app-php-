<?php
session_start();
$isLoggedIn = isset($_SESSION['user_nic']); // ✅ use user_nic
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinePay.com</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            mrgin: 0;
            background: rgb(2, 44, 86);
            font-family: Arial, sans-serif;
            overflow-x: hidden;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background: rgb(5, 12, 146);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
        }

        .login-btn, .admin-btn, .logout-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .login-btn { background-color: #007bff; }
        .admin-btn { background-color: #007bff; }
        .logout-btn { background-color: rgb(234, 17, 17); }

        .login-btn:hover { background-color: #0056b3; transform: scale(1.05); }
        .logout-btn:hover { background-color: rgb(219, 121, 121); transform: scale(1.05); }
        .admin-btn:hover { background-color: #0056b3; transform: scale(1.05); }

        .login_admin-space { gap: 20px; display: flex; }

        .gallery-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            padding: 40px 0;
        }

        .gallery-track {
            display: flex;
            width: max-content;
            animation: scrollGallery 30s linear infinite;
        }

        .gallery-track img {
            height: 200px;
            margin: 0 15px;
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .gallery-track img:hover {
            transform: scale(1.1);
            cursor: pointer;
        }

        @keyframes scrollGallery {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        footer {
            background-color: #001f3f;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        footer ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        footer a {
            color: white;
            text-decoration: none;
        }

        .content {
            font-size: 20px;
            text-align: center;
            padding: 50px;
            color: white;
        }

        /* Popup styles */
        .popup-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .popup-content {
            background-color: white;
            color: black;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .popup-content h2, .popup-content h3 {
            color: #003366;
        }
    </style>
</head>
<body>

<header>
    <div class="logo-title">
        <img src="images/images/shield.png" alt="shield" class="shield">
        <img src="images/images/Fine Pay.png" alt="Logo" class="logo">
    </div>

    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <li><a href="user_dashboard.php">Dashboard</a></li>
            <?php endif; ?>
            <li><a href="#" onclick="openAboutUs()">About Us</a></li>
            
        </ul>
    </nav>

    <div class="login_admin-space">
        <?php if ($isLoggedIn): ?>
            <button class="logout-btn"><a href="logout.php" style="color:white; text-decoration:none;">Logout</a></button>
        <?php else: ?>
            <button class="login-btn"><a href="login.php" style="color:white; text-decoration:none;">Login</a></button>
        <?php endif; ?>
        <button class="admin-btn"><a href="admin_login.php" style="color:white; text-decoration:none;">Admin</a></button>
    </div>
</header>

<main>
    <section class="content">
        <h2>Welcome to Fine Pay</h2>
        <p>Pay your fines easily and securely online.</p>
    </section>
</main>

<!-- Gallery Section -->
<div class="gallery-container">
    <div class="gallery-track">
        <div class="set">
            <img src="images/gallery_Images/img_1.jpg" alt="Image 1">
            <img src="images/gallery_Images/img_2.png" alt="Image 2">
            <img src="images/gallery_Images/img_3.jpg" alt="Image 3">
            <img src="images/gallery_Images/img_4.jpg" alt="Image 4">
            <img src="images/gallery_Images/img_5.jpg" alt="Image 5">
        </div>
        <div class="set">
            <img src="images/gallery_Images/img_6.jpg" alt="Image 6">
            <img src="images/gallery_Images/img_7.jpg" alt="Image 7">
            <img src="images/gallery_Images/img_8.jpg" alt="Image 8">
            <img src="images/gallery_Images/img_9.jpg" alt="Image 9">
            <img src="images/gallery_Images/img_10.jpg" alt="Image 10">
        </div>
    </div>
</div>

<!-- About Us Popup Modal -->
<div id="aboutUsModal" class="popup-overlay">
    <div class="popup-content">
        <span class="close-btn" onclick="closeAboutUs()">&times;</span>
        <h2>About FinePay.com</h2>
        <p><strong>FinePay.com</strong> is a modern digital platform designed to help you manage and pay your police traffic fines easily, securely, and efficiently.</p>
        <h3>Our Mission</h3>
        <p>To provide a secure, transparent, and user-friendly platform for managing and paying police fines online, in collaboration with the Sri Lanka Police Department.</p>
        <h3>What We Do</h3>
        <ul>
            <li>Enable users to check their outstanding traffic fines</li>
            <li>Offer secure online payment methods</li>
            <li>Generate instant digital receipts</li>
            <li>Ensure user data privacy and legal compliance</li>
        </ul>
        <h3>Why Choose FinePay?</h3>
        <p>Say goodbye to long queues, manual forms, and missed deadlines. Our platform is available 24/7, ensuring you can make payments anytime, anywhere.</p>
        <h3>Security & Trust</h3>
        <p>We use the latest encryption technology to protect your data and ensure all transactions are processed safely.</p>
        <h3>Need Help?</h3>
        <p>Visit our <a href="contact_us.php">Contact Us</a> page or check the FAQ section.</p>
    </div>
</div>

<footer>
    <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Terms of Use</a></li>
    </ul>
    <p>&copy; 2025 onwards. All rights reserved.</p>
</footer>

<!-- JavaScript -->
<script>
function openAboutUs() {
    document.getElementById("aboutUsModal").style.display = "block";
}

function closeAboutUs() {
    document.getElementById("aboutUsModal").style.display = "none";
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById("aboutUsModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>
