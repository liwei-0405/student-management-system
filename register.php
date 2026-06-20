<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div class="container mt-5 auth-container">
    <h2 class="text-center mb-4">Register</h2>
    <p class="auth-message">If You Have Any Account Please Login</p>

    <?php
    include 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password_input = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password_input !== $confirm_password) {
            echo "<div class='alert alert-danger'>Password and confirm password do not match.</div>";
        } elseif (strlen($password_input) < 8) {
            echo "<div class='alert alert-danger'>Password must be at least 8 characters long.</div>";
        } else {
            $check_sql = "SELECT id FROM users WHERE username = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                echo "<div class='alert alert-danger'>Username already exists. Please choose another username.</div>";
            } else {
                $password = password_hash($password_input, PASSWORD_BCRYPT);

                $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $username, $password);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Registration successful! You can now <a href='login.php'>login</a>.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Registration failed. Please try again.</div>";
                }

                $stmt->close();
            }

            $check_stmt->close();
        }
    }
    ?>

    <form method="POST" action="" class="card p-4">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" id="username"
value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="login.php" class="btn btn-primary">Login</a>
        </div>
    </form>
</div>
</body>
</html>