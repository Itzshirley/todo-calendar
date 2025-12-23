<?php
session_start();
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email");
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-primary">

<div class="container mt-5">
    <div class="card p-4 shadow">
        <h3 class="text-center">Login</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
            <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
            <button class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-3 text-center">
            No account?
            <a href="register.php">Register</a>
        </p>
    </div>
</div>

</body>
</html>
