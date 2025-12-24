<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId   = $_SESSION["user_id"];
$category = $_GET["category"] ?? "";

/* =====================
   ADD TASK
===================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_task"])) {
    $stmt = $pdo->prepare("
        INSERT INTO tasks (user_id, task, task_date, status, category)
        VALUES (:u, :t, :d, :s, :c)
    ");
    $stmt->execute([
        "u" => $userId,
        "t" => $_POST["task"],
        "d" => $_POST["task_date"],
        "s" => $_POST["status"],
        "c" => $category
    ]);
}

/* =====================
   UPDATE STATUS
===================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET status = :s
        WHERE id = :id AND user_id = :u
    ");
    $stmt->execute([
        "s"  => $_POST["status"],
        "id" => $_POST["task_id"],
        "u"  => $userId
    ]);
}

/* =====================
   DELETE TASK
===================== */
if (isset($_GET["delete"])) {
    $stmt = $pdo->prepare("
        DELETE FROM tasks
        WHERE id = :id AND user_id = :u
    ");
    $stmt->execute([
        "id" => $_GET["delete"],
        "u"  => $userId
    ]);
}

/* =====================
   FETCH TASKS
===================== */
$stmt = $pdo->prepare("
    SELECT id, task, task_date, status
    FROM tasks
    WHERE user_id = :u AND category = :c
    ORDER BY task_date
");
$stmt->execute([
    "u" => $userId,
    "c" => $category
]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title><?= ucfirst($category) ?> Tasks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-secondary btn-sm mb-3">⬅ Back</a>

<h4><?= ucfirst($category) ?> Tasks</h4>

<!-- ADD TASK -->
<form method="POST" class="card p-3 mb-4">
<input type="text" name="task" class="form-control mb-2" placeholder="Task description" required>
<input type="date" name="task_date" class="form-control mb-2" required>

<select name="status" class="form-control mb-2">
    <option value="pending">⏳ Pending</option>
    <option value="in_progress">🚧 In Progress</option>
    <option value="done">✅ Done</option>
</select>

<button name="add_task" class="btn btn-primary">Add Task</button>
</form>

<!-- TASK LIST -->
<table class="table table-bordered">
<tr>
<th>Date</th><th>Task</th><th>Status</th><th>Delete</th>
</tr>

<?php foreach ($tasks as $t): ?>
<tr>
<td><?= htmlspecialchars($t["task_date"]) ?></td>
<td><?= htmlspecialchars($t["task"]) ?></td>

<td>
<form method="POST">
<input type="hidden" name="task_id" value="<?= $t["id"] ?>">
<select name="status" onchange="this.form.submit()" class="form-control">
<option value="pending" <?= $t["status"]==="pending"?"selected":"" ?>>⏳ Pending</option>
<option value="in_progress" <?= $t["status"]==="in_progress"?"selected":"" ?>>🚧 In Progress</option>
<option value="done" <?= $t["status"]==="done"?"selected":"" ?>>✅ Done</option>
</select>
<input type="hidden" name="update_status">
</form>
</td>

<td>
<a href="?category=<?= $category ?>&delete=<?= $t["id"] ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Delete task?')">❌</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
