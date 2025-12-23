<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION["name"];

$month = $_GET["month"] ?? date("m");
$year  = $_GET["year"] ?? date("Y");

$month = (int)$month;
$year  = (int)$year;

$firstDay = strtotime("$year-$month-01");
$daysInMonth = date("t", $firstDay);
$startDay = date("w", $firstDay);

// Fetch events
$stmt = $pdo->prepare("SELECT event_date, type FROM events");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-primary text-white">

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Dashboard — Welcome, <?= htmlspecialchars($name) ?></h4>

        <select class="form-select w-auto">
            <option>Blue</option>
            <option>Dark</option>
            <option>Light</option>
        </select>
    </div>

    <!-- CATEGORY BUTTONS -->
    <div class="row text-center mb-4">
        <?php
        $cats = ["Work", "School", "Health", "Fitness", "Others"];
        foreach ($cats as $cat):
        ?>
            <div class="col-md-2 mb-2">
                <a href="category.php?type=<?= strtolower($cat) ?>"
                   class="btn btn-light w-100"><?= $cat ?></a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- CALENDAR HEADER -->
    <div class="card text-dark p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">

            <!-- PREVIOUS MONTH -->
            <a class="btn btn-outline-primary"
               href="?month=<?= $month-1 ?>&year=<?= $year ?>">◀</a>

            <!-- MONTH + YEAR -->
            <h5>
                <?= date("F", $firstDay) ?>
                <select onchange="location.href='?month=<?= $month ?>&year='+this.value">
                    <?php for ($y = 2025; $y <= 2035; $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? "selected" : "" ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </h5>

            <!-- NEXT MONTH -->
            <a class="btn btn-outline-primary"
               href="?month=<?= $month+1 ?>&year=<?= $year ?>">▶</a>
        </div>

        <!-- CALENDAR TABLE -->
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <?php foreach (["Sun","Mon","Tue","Wed","Thu","Fri","Sat"] as $d): ?>
                        <th><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php
                for ($i=0; $i<$startDay; $i++) echo "<td></td>";

                for ($day=1; $day<=$daysInMonth; $day++):
                    $date = "$year-$month-".str_pad($day,2,"0",STR_PAD_LEFT);

                    if (($day+$startDay-1)%7==0 && $day!=1) echo "</tr><tr>";
                ?>
                    <td>
                        <a href="diary.php?date=<?= $date ?>" class="text-decoration-none">
                            <?= $day ?>
                        </a>

                        <?php
                        if (isset($events[$date])) {
                            foreach ($events[$date] as $type) {
                                echo $type === "holiday" ? " 🎉" : " 🔔";
                            }
                        }
                        ?>
                    </td>
                <?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
