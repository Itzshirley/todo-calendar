<?php
session_start();
require "db.php";

/* =====================
   AUTH CHECK
===================== */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$category = $_GET["category"] ?? "";

if (!$category) {
    die("Category not specified.");
}

/* =====================
   FETCH TASKS BY CATEGORY
===================== */
$stmt = $pdo->prepare("
    SELECT task_id, task, task_date, status
    FROM tasks
    WHERE user_id = :uid
      AND category = :cat
    ORDER BY task_date
");
$stmt->execute([
    "uid" => $userId,
    "cat" => $category
]);

$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= ucfirst($category) ?> Tasks</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-secondary btn-sm mb-3">⬅ Back to Dashboard</a>

<h4><?= ucfirst($category) ?> Tasks</h4>

<?php if (!$tasks): ?>
<p>No tasks in this category.</p>
<?php endif; ?>

<table class="table table-bordered mt-3">
<thead class="table-light">
<tr>
    <th>Date</th>
    <th>Task</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
<?php foreach ($tasks as $task): ?>
<tr>
    <td><?= htmlspecialchars($task["task_date"]) ?></td>
    <td><?= htmlspecialchars($task["task"]) ?></td>
    <td><?= htmlspecialchars($task["status"]) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>

</body>
</html>
