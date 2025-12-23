<?php
session_start();
require_once "db.php";

$date = $_GET["date"];
$userId = $_SESSION["user_id"];

$stmt = $pdo->prepare(
    "SELECT content FROM diary_entries
     WHERE user_id=:u AND entry_date=:d"
);
$stmt->execute(["u"=>$userId, "d"=>$date]);
$entry = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-primary text-white">
<div class="container mt-4">
    <h4>Diary — <?= $date ?></h4>

    <form method="POST" action="save_diary.php">
        <input type="hidden" name="date" value="<?= $date ?>">
        <textarea class="form-control mb-3" name="content" rows="6"><?= htmlspecialchars($entry) ?></textarea>
        <button class="btn btn-light">Save</button>
    </form>
</div>
</body>
</html>
