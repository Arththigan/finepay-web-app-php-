<?php
session_start();

// Optional: Show success or error messages after form submission
$success_msg = $_SESSION['contact_success'] ?? '';
$error_msg = $_SESSION['contact_error'] ?? '';
unset($_SESSION['contact_success'], $_SESSION['contact_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Contact Us - FinePay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 40px;
    }
    .contact-form {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    h2 {
      color: #3e4196;
      margin-bottom: 20px;
      text-align: center;
    }
    label {
      font-weight: 600;
    }
    .btn-submit {
      background-color: #3e4196;
      border: none;
    }
    .btn-submit:hover {
      background-color: #5a60c3;
    }
  </style>
</head>
<body>

<div class="contact-form">
  <h2>Contact Administration</h2>

  <?php if ($success_msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
  <?php endif; ?>

  <form action="contact_submit.php" method="post" novalidate>
    <div class="mb-3">
      <label for="name" class="form-label">Your Name *</label>
      <input type="text" name="name" id="name" class="form-control" required maxlength="100" />
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Your Email *</label>
      <input type="email" name="email" id="email" class="form-control" required maxlength="150" />
    </div>

    <div class="mb-3">
      <label for="subject" class="form-label">Subject *</label>
      <input type="text" name="subject" id="subject" class="form-control" required maxlength="150" />
    </div>

    <div class="mb-3">
      <label for="message" class="form-label">Message *</label>
      <textarea name="message" id="message" class="form-control" rows="5" required maxlength="1000"></textarea>
    </div>

    <button type="submit" class="btn btn-submit text-white w-100">Send Message</button>
  </form>
</div>

</body>
</html>
