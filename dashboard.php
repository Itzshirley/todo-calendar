<?php
session_start();
require "db.php";

/* AUTH CHECK */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$name   = $_SESSION["name"] ?? "User";

/* MONTH & YEAR */
$month = isset($_GET["month"]) ? (int)$_GET["month"] : date("n");
$year  = isset($_GET["year"])  ? (int)$_GET["year"]  : date("Y");

/* FETCH TASKS */
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

$taskMap = [];
foreach ($stmt as $row) {
    $taskMap[$row["task_date"]][] = $row["status"];
}

/* KENYAN PUBLIC HOLIDAYS (STATIC LIST) */
$holidays = [
    "$year-01-01" => "🎉 New Year",
    "$year-05-01" => "👷 Labour Day",
    "$year-06-01" => "🇰🇪 Madaraka Day",
    "$year-10-20" => "🛡️ Mashujaa Day",
    "$year-12-12" => "🇰🇪 Jamhuri Day",
    "$year-12-25" => "🎄 Christmas",
];

/* CALENDAR LOGIC */
$firstDay   = strtotime("$year-$month-01");
$totalDays = date("t", $firstDay);
$startDay  = date("N", $firstDay); // Monday = 1
$today     = date("Y-m-d");
$monthName = date("F", $firstDay);
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}
.day {
    background: #f8f9fa;
    padding: 10px;
    min-height: 100px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.9rem;
}
.day:hover {
    background: #e3f2fd;
}
.today {
    border: 2px solid #0d6efd;
}
.holiday {
    background: #fff3cd;
}
.header {
    font-weight: bold;
    text-align: center;
}
.category-btn {
    width: 100%;
    font-size: 1.05rem;
}
.small-text {
    font-size: 0.75rem;
}
</style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary px-3">
    <span class="navbar-brand">📅 Smart Planner</span>
    <span class="text-white">Welcome, <?= htmlspecialchars($name) ?></span>
    <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
</nav>

<div class="container mt-4">

<!-- CATEGORY BUTTONS -->
<div class="row mb-4 text-center">
    <div class="col">
        <a href="tasks.php?category=work" class="btn btn-outline-primary category-btn">💼 Work</a>
    </div>
    <div class="col">
        <a href="tasks.php?category=school" class="btn btn-outline-primary category-btn">🎓 School</a>
    </div>
    <div class="col">
        <a href="tasks.php?category=health" class="btn btn-outline-primary category-btn">❤️ Health</a>
    </div>
    <div class="col">
        <a href="tasks.php?category=personal" class="btn btn-outline-primary category-btn">🏠 Personal</a>
    </div>
</div>

<!-- MONTH NAVIGATION -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a class="btn btn-secondary"
       href="dashboard.php?month=<?= $month-1 < 1 ? 12 : $month-1 ?>&year=<?= $month-1 < 1 ? $year-1 : $year ?>">
       ⬅
    </a>

    <h4><?= $monthName ?> <?= $year ?></h4>

    <a class="btn btn-secondary"
       href="dashboard.php?month=<?= $month+1 > 12 ? 1 : $month+1 ?>&year=<?= $month+1 > 12 ? $year+1 : $year ?>">
       ➡
    </a>
</div>

<!-- WEEK HEADERS -->
<div class="calendar mb-2">
    <div class="header">Mon</div>
    <div class="header">Tue</div>
    <div class="header">Wed</div>
    <div class="header">Thu</div>
    <div class="header">Fri</div>
    <div class="header">Sat</div>
    <div class="header">Sun</div>
</div>

<!-- CALENDAR -->
<div class="calendar">
<?php
/* EMPTY CELLS */
for ($i = 1; $i < $startDay; $i++) {
    echo "<div></div>";
}

/* DAYS */
for ($day = 1; $day <= $totalDays; $day++) {
    $date = sprintf("%04d-%02d-%02d", $year, $month, $day);

    $classes = "day";
    if ($date === $today) $classes .= " today";
    if (isset($holidays[$date])) $classes .= " holiday";

    /* TASK ICONS */
    $icons = "";
    if (isset($taskMap[$date])) {
        foreach ($taskMap[$date] as $s) {
            $icons .= $s === "done" ? "✅ " : ($s === "in_progress" ? "🚧 " : "⏳ ");
        }
    }

    /* HOLIDAY LABEL */
    $holidayText = isset($holidays[$date])
        ? "<div class='small-text text-danger'>{$holidays[$date]}</div>"
        : "";

    echo "
    <div class='$classes' onclick=\"openDiary('$date')\">
        <strong>$day</strong><br>
        $icons
        $holidayText
    </div>";
}
?>
</div>

</div>

<script>
function openDiary(date){
    window.location = "diary.php?date=" + date;
}
</script>

</body>
</html>
