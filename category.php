<?php
session_start();
require_once "db.php";

$type = $_GET["type"];
$user = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare(
        "INSERT INTO tasks (user_id, category, task_date, title, status)
         VALUES (:u,:c,:d,:t,:s)"
    );
    $stmt->execute([
        "u"=>$user,
        "c"=>$type,
        "d"=>$_POST["date"],
        "t"=>$_POST["title"],
        "s"=>$_POST["status"]
    ]);
}

$tasks = $pdo->prepare(
    "SELECT * FROM tasks WHERE user_id=:u AND category=:c"
);
$tasks->execute(["u"=>$user,"c"=>$type]);
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-primary text-white">

<div class="container mt-4">
<h3><?= ucfirst($type) ?> Tasks</h3>

<form method="POST" class="row g-2">
<input type="date" name="date" class="form-control col" required>
<input type="text" name="title" class="form-control col" placeholder="Task">
<select name="status" class="form-control col">
<option value="pending">⏳ Pending</option>
<option value="progress">🚧 In Progress</option>
<option value="done">✅ Done</option>
</select>
<button class="btn btn-light col">Add</button>
</form>

<hr>

<?php foreach ($tasks as $t): ?>
<div class="card p-2 mb-2">
<?= $t["task_date"] ?> — <?= $t["title"] ?> (<?= $t["status"] ?>)
</div>
<?php endforeach; ?>

<a href="dashboard.php" class="btn btn-light mt-3">⬅ Back</a>
</div>
</body>
</html>

