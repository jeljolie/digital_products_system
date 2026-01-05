<?php
include 'config.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check required fields
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if username exists
        $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $username, $email, $hashed);

            if ($stmt->execute()) {
                $success = "Registration successful. You can now login.";
            } else {
                $error = "Something went wrong";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Digital Products System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        background-image: url(download.gif);
        background-size: cover;
        background-position: center;
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="width: 400px;">
    <h3 class="text-center mb-4">Register</h3>

    <!-- SHOW MESSAGES HERE -->
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <div class="text-center mt-3">
        <small>Already have an account? <a href="login.php">Login here</a></small>
    </div>
</div>

</body>
</html>