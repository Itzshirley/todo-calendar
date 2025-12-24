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

$firstDay      = strtotime("$year-$month-01");
$daysInMonth   = date("t", $firstDay);
$startDay      = date("N", $firstDay);
$today         = date("Y-m-d");

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
   KENYAN HOLIDAYS
===================== */
$holidays = [
    "$year-01-01" => "New Year 🎉",
    "$year-05-01" => "Labour Day 🛠️",
    "$year-06-01" => "Madaraka 🇰🇪",
    "$year-10-20" => "Mashujaa 💪",
    "$year-12-12" => "Jamhuri 🇰🇪",
    "$year-12-25" => "Christmas 🎄"
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
.today {background:#cce5ff;}
.holiday {background:#ffe6e6;font-size:0.75em;}
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

<!-- MONTH NAV -->
<div class="d-flex justify-content-between align-items-center mb-2">
<button class="btn btn-outline-secondary" onclick="changeMonth(-1)">⬅</button>
<h5><?= date("F Y", $firstDay) ?></h5>
<button class="btn btn-outline-secondary" onclick="changeMonth(1)">➡</button>
</div>

<!-- CALENDAR -->
<div class="calendar mb-5">

<?php
for ($i=1; $i<$startDay; $i++) echo "<div></div>";

for ($d=1; $d<=$daysInMonth; $d++) {
    $date = sprintf("%04d-%02d-%02d", $year, $month, $d);
    $class = "day";

    if ($date === $today) $class .= " today";
    if (isset($holidays[$date])) $class .= " holiday";

    echo "<div class='$class' onclick=\"openDiary('$date')\">
            $d<br>".($holidays[$date] ?? "")."
          </div>";
}
?>

</div>

</div>

<script>
function changeMonth(step){
    let m = <?= $month ?> + step;
    let y = <?= $year ?>;
    if(m < 1){ m = 12; y--; }
    if(m > 12){ m = 1; y++; }
    window.location = "?month="+m+"&year="+y;
}
function openDiary(date){
    window.location = "diary.php?date="+date;
}
</script>

</body>
</html>
