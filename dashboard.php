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

$userId   = $_SESSION["user_id"];
$userName = $_SESSION["name"] ?? "User";

/* =====================
   DATE HANDLING
===================== */
$month = isset($_GET["month"]) ? (int)$_GET["month"] : (int)date("m");
$year  = isset($_GET["year"])  ? (int)$_GET["year"]  : (int)date("Y");

$firstDay    = strtotime("$year-$month-01");
$daysInMonth = date("t", $firstDay);
$startDay    = date("N", $firstDay);
$today       = date("Y-m-d");

/* =====================
   WEEK RANGE
===================== */
$weekStart = date("Y-m-d", strtotime("monday this week"));
$weekEnd   = date("Y-m-d", strtotime("sunday this week"));

/* =====================
   WEEKLY TASK SUMMARY
===================== */
$stmt = $pdo->prepare("
    SELECT task, task_date, status
    FROM tasks
    WHERE user_id = :uid
      AND task_date BETWEEN :ws AND :we
    ORDER BY task_date
");
$stmt->execute([
    "uid" => $userId,
    "ws"  => $weekStart,
    "we"  => $weekEnd
]);
$weeklyTasks = $stmt->fetchAll();

/* =====================
   TASK INDICATORS (MONTH)
===================== */
$stmt = $pdo->prepare("
    SELECT task_date, status
    FROM tasks
    WHERE user_id = :uid
      AND EXTRACT(MONTH FROM task_date) = :m
      AND EXTRACT(YEAR FROM task_date) = :y
");
$stmt->execute([
    "uid" => $userId,
    "m"   => $month,
    "y"   => $year
]);

$taskIndicators = [];
foreach ($stmt->fetchAll() as $t) {
    $taskIndicators[$t["task_date"]][] = $t["status"];
}

/* =====================
   KENYAN HOLIDAYS
===================== */
$holidays = [
    "$year-01-01" => "🎉",
    "$year-05-01" => "🛠️",
    "$year-06-01" => "🇰🇪",
    "$year-10-20" => "💪",
    "$year-12-12" => "🇰🇪",
    "$year-12-25" => "🎄"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.calendar {
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:5px;
}
.day {
    border:1px solid #ccc;
    padding:10px;
    text-align:center;
    cursor:pointer;
    background:#fff;
}
.today {
    background:#cce5ff;
}
.indicators {
    font-size:0.8em;
}
</style>
</head>

<body class="bg-light">

<div class="container mt-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>👋 Welcome, <?= htmlspecialchars($userName) ?></h4>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
</div>

<p><strong>Today:</strong> <?= date("l, d F Y") ?></p>

<!-- CATEGORY BUTTONS -->
<div class="mb-4">
    <a href="tasks.php?category=work" class="btn btn-primary">💼 Work</a>
    <a href="tasks.php?category=school" class="btn btn-success">🎓 School</a>
    <a href="tasks.php?category=health" class="btn btn-danger">❤️ Health</a>
    <a href="tasks.php?category=personal" class="btn btn-warning">🏠 Personal</a>
</div>

<!-- WEEKLY SUMMARY -->
<div class="card p-3 mb-4">
<h5>📊 Weekly Tasks (<?= $weekStart ?> → <?= $weekEnd ?>)</h5>

<?php if (!$weeklyTasks): ?>
<p>No tasks this week.</p>
<?php endif; ?>

<?php foreach ($weeklyTasks as $task): ?>
<div>
📌 <?= htmlspecialchars($task["task_date"]) ?> —
<?= htmlspecialchars($task["task"]) ?>
<span class="badge bg-secondary"><?= htmlspecialchars($task["status"]) ?></span>
</div>
<?php endforeach; ?>
</div>

<!-- MONTH NAVIGATION -->
<div class="d-flex justify-content-between align-items-center mb-2">
<button class="btn btn-outline-secondary" onclick="changeMonth(-1)">⬅</button>
<h5><?= date("F Y", $firstDay) ?></h5>
<button class="btn btn-outline-secondary" onclick="changeMonth(1)">➡</button>
</div>

<!-- CALENDAR -->
<div class="calendar mb-5">
<?php
for ($i = 1; $i < $startDay; $i++) {
    echo "<div></div>";
}

for ($d = 1; $d <= $daysInMonth; $d++) {
    $date = sprintf("%04d-%02d-%02d", $year, $month, $d);
    $class = "day";
    if ($date === $today) $class .= " today";

    $icons = "";
    if (isset($taskIndicators[$date])) {
        foreach ($taskIndicators[$date] as $st) {
            if ($st === "pending") $icons .= "⏳ ";
            if ($st === "in_progress") $icons .= "🚧 ";
            if ($st === "done") $icons .= "✅ ";
        }
    }

    echo "<div class='$class' onclick=\"openDiary('$date')\">
            <strong>$d</strong><br>
            <span class='indicators'>$icons ".($holidays[$date] ?? "")."</span>
          </div>";
}
?>
</div>

</div>

<script>
function changeMonth(step){
    let m = <?= $month ?> + step;
    let y = <?= $year ?>;
    if (m < 1) { m = 12; y--; }
    if (m > 12) { m = 1; y++; }
    window.location = "?month=" + m + "&year=" + y;
}
function openDiary(date){
    window.location = "diary.php?date=" + date;
}
</script>

</body>
</html>
