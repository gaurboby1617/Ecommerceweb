<?php
include 'include/conn.php'; // adjust path agar zaroorat ho

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Password and Confirm Password do not match.";
    } else {
        // check duplicate email
        $check = mysqli_query($conn, "SELECT id FROM admin_users WHERE email='" . mysqli_real_escape_string($conn, $email) . "'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO admin_users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful. Please login.";
            } else {
                $error = "Something went wrong. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 400px; border: none; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="text-center mb-3">Admin Sign Up</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="signin.php">Login</a></p>
        </div>
    </div>
</body>
</html>