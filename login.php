<?php
session_start();
require "db.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"]=="POST") {
$stmt=$pdo->prepare("SELECT * FROM users WHERE email=:e");
$stmt->execute(["e"=>$_POST["email"]]);
$user=$stmt->fetch();

if($user && password_verify($_POST["password"],$user["password"])){
$_SESSION["user_id"]=$user["id"];
$_SESSION["name"]=$user["name"];
header("Location: dashboard.php");
exit;
}
$error="Invalid login";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width:400px;">
<h3 class="text-center">Login</h3>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" class="card p-3">
<input name="email" class="form-control mb-2" placeholder="Email" required>
<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
<button class="btn btn-dark">Login</button>
</form>
</div>
</body>
</html>
