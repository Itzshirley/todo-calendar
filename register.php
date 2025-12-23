<?php
// register.php
include "db.php";

$error = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if ($username == "" || $email == "" || $password == "") {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $check = $conn->query("SELECT id FROM users WHERE email='$email'");

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $conn->query(
                "INSERT INTO users (username, email, password)
                 VALUES ('$username', '$email', '$hashedPassword')"
            );

            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | To-Do Calendar App</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="d-flex justify-content-center align-items-center">

<div class="card p-4 shadow" style="width:420px;">
    <h3 class="text-center mb-3">Create Account</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <input
            type="text"
            name="username"
            class="form-control mb-2"
            placeholder="Username"
            required
        >

        <input
            type="email"
            name="email"
            class="form-control mb-2"
            placeholder="Email address"
            required
        >

        <input
            type="password"
            name="password"
            class="form-control mb-3"
            placeholder="Password"
            required
        >

        <button
            type="submit"
            name="register"
            class="btn btn-blue w-100"
        >
            Register
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="index.php">← Back to Home</a><br>
        <a href="login.php">Already have an account? Login</a>
    </div>
</div>

</body>
</html>


