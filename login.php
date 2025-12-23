<?php
include "db.php";

$error = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            // ✅ REDIRECT AFTER LOGIN
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="d-flex justify-content-center align-items-center">

<div class="card p-4 shadow" style="width:400px;">
<h3 class="text-center mb-3">Login</h3>

<?php if ($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
    <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>

    <!-- IMPORTANT -->
    <button class="btn btn-blue w-100" type="submit" name="login">
        Login
    </button>
</form>

<div class="text-center mt-3">
    <a href="register.php">No account? Register</a>
</div>
</div>

</body>
</html>


