<?php
session_start();
require_once "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    if ($password !== $confirm) {
        $error = "Passwords do not match";
    } else {
        try {
            // Check if email already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $check->execute(["email" => $email]);

            if ($check->fetch()) {
                $error = "Email already registered";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password)
                     VALUES (:name, :email, :password)"
                );

                $stmt->execute([
                    "name" => $name,
                    "email" => $email,
                    "password" => $hashedPassword
                ]);

                $success = "Registration successful! You can now log in.";
            }
        } catch (PDOException $e) {
            $error = "Registration failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-primary">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card p-4 shadow">
                <h3 class="text-center mb-3">Create Account</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input
                        class="form-control mb-3"
                        type="text"
                        name="name"
                        placeholder="Full Name"
                        required
                    >

                    <input
                        class="form-control mb-3"
                        type="email"
                        name="email"
                        placeholder="Email Address"
                        required
                    >

                    <input
                        class="form-control mb-3"
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    >

                    <input
                        class="form-control mb-3"
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm Password"
                        required
                    >

                    <button class="btn btn-primary w-100">
                        Register
                    </button>
                </form>

                <p class="text-center mt-3">
                    Already have an account?
                    <a href="login.php">Login</a>
                </p>
            </div>

        </div>
    </div>
</div>

</body>
</html>
