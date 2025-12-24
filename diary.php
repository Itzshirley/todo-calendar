<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$date   = $_GET["date"] ?? date("Y-m-d");

/* ADD TASK */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task     = $_POST["task"];
    $status   = $_POST["status"];
    $category = $_POST["category"];

    $stmt = $pdo->prepare("
        INSERT INTO tasks (user_id, task, task_date, status, category)
        VALUES (:uid, :task, :d, :s, :c)
    ");
    $stmt->execute([
        "uid"=>$userId,
        "task"=>$task,
        "d"=>$date,
        "s"=>$status,
        "c"=>$category
    ]);
}

/* DELETE TASK */
if (isset($_GET["delete"])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=:id AND user_id=:uid");
    $stmt->execute([
        "id"=>$_GET["delete"],
        "uid"=>$userId
    ]);
}

/* FETCH TASKS */
$stmt = $pdo->prepare("
    SELECT * FROM tasks
    WHERE user_id=:uid AND task_date=:d
    ORDER BY id DESC
");
$stmt->execute([
    "uid"=>$userId,
    "d"=>$date
]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Diary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
<a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

<h4>📅 Diary — <?= htmlspecialchars($date) ?></h4>

<form method="POST" class="card p-3 mb-4">
<input name="task" class="form-control mb-2" placeholder="Task description" required>

<select name="category" class="form-select mb-2">
<option value="work">💼 Work</option>
<option value="school">🎓 School</option>
<option value="health">❤️ Health</option>
<option value="personal">🏠 Personal</option>
</select>

<select name="status" class="form-select mb-2">
<option value="pending">⏳ Pending</option>
<option value="in_progress">🚧 In Progress</option>
<option value="done">✅ Done</option>
</select>

<button class="btn btn-primary">Add Task</button>
</form>

<?php foreach ($tasks as $t): ?>
<div class="card p-2 mb-2">
<strong><?= htmlspecialchars($t["task"]) ?></strong>
<div><?= $t["status"] ?> | <?= $t["category"] ?></div>
<a href="?date=<?= $date ?>&delete=<?= $t["id"] ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
</div>
<?php endforeach; ?>
</div>
</body>
</html>
