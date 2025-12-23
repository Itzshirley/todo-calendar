<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$userName = $_SESSION["name"] ?? "User";

/* ======================
   DATE / CALENDAR LOGIC
====================== */
$month = $_GET["month"] ?? date("m");
$year  = $_GET["year"] ?? date("Y");

$firstDay = strtotime("$year-$month-01");
$daysInMonth = date("t", $firstDay);
$startDay = date("N", $firstDay);

/* ======================
   WEEKLY SUMMARY
====================== */
$weekStart = date("Y-m-d", strtotime("monday this week"));
$weekEnd   = date("Y-m-d", strtotime("sunday this week"));

$stmt = $pdo->prepare("
    SELECT title, task_date, status
    FROM tasks
    WHERE user_id = :u
      AND task_date BETWEEN :s AND :e
    ORDER BY task_date
");
$stmt->execute([
    "u" => $userId,
    "s" => $weekStart,
    "e" => $weekEnd
]);
$weeklyTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ======================
   KENYAN PUBLIC HOLIDAYS
====================== */
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
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.calendar {display:grid;grid-template-columns:repeat(7,1fr);gap:5px;}
.day {padding:10px;border:1px solid #ddd;text-align:center;cursor:pointer;}
.today {background:#cce5ff;}
.holiday {background:#ffe5e5;font-size:0.8em;}
</style>
</head>
<body class="bg-light">

<div class="container mt-4">

<h4>👋 Welcome, <?= htmlspecialchars($userName) ?></h4>
<p><strong>Today:</strong> <?= date("l, d F Y") ?></p>

<a href="logout.php" class="btn btn-danger btn-sm mb-3">Logout</a>

<!-- CATEGORY BUTTONS -->
<div class="mb-3">
    <a href="tasks.php?cat=work" class="btn btn-primary">💼 Work</a>
    <a href="tasks.php?cat=school" class="btn btn-success">🎓 School</a>
    <a href="tasks.php?cat=health" class="btn btn-danger">❤️ Health</a>
    <a href="tasks.php?cat=personal" class="btn btn-warning">🏠 Personal</a>
</div>

<!-- WEEKLY SUMMARY -->
<div class="card mb-4 p-3">
<h5>📊 Weekly Task Summary</h5>
<small><?= $weekStart ?> → <?= $weekEnd ?></small>

<?php if (!$weeklyTasks): ?>
<p class="mt-2">No tasks this week.</p>
<?php endif; ?>

<?php foreach ($weeklyTasks as $task): ?>
<div>
📌 <?= htmlspecialchars($task["task_date"]) ?> —
<?= htmlspecialchars($task["title"]) ?>
<span class="badge bg-secondary"><?= htmlspecialchars($task["status"]) ?></span>
</div>
<?php endforeach; ?>
</div>

<!-- CALENDAR NAVIGATION -->
<div class="d-flex justify-content-between align-items-center mb-2">
<button class="btn btn-outline-secondary" onclick="navigate(-1)">⬅</button>
<h5><?= date("F", $firstDay) ?> <?= $year ?></h5>
<button class="btn btn-outline-secondary" onclick="navigate(1)">➡</button>
</div>

<!-- CALENDAR -->
<div class="calendar">
<?php
for ($i=1; $i<$startDay; $i++) echo "<div></div>";

for ($d=1; $d<=$daysInMonth; $d++) {
    $date = "$year-$month-".str_pad($d,2,"0",STR_PAD_LEFT);
    $class = "day";
    if ($date == date("Y-m-d")) $class .= " today";
    if (isset($holidays[$date])) $class .= " holiday";

    echo "<div class='$class' onclick=\"openDiary('$date')\">
          $d<br>".($holidays[$date] ?? "")."
          </div>";
}
?>
</div>

</div>

<script>
function navigate(step){
    let m = <?= $month ?> + step;
    let y = <?= $year ?>;
    if(m<1){m=12;y--}
    if(m>12){m=1;y++}
    window.location = `?month=${m}&year=${y}`;
}
function openDiary(date){
    window.location = "diary.php?date="+date;
}
</script>

</body>
</html>
