<?php
session_start();
require_once "db.php";

$user = $_SESSION["user_id"];
$date = $_GET["date"];

$stmt = $pdo->prepare(
 "SELECT content FROM diary_entries WHERE user_id=:u AND entry_date=:d"
);
$stmt->execute(["u"=>$user,"d"=>$date]);
$content = $stmt->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pdo->prepare(
     "INSERT INTO diary_entries (user_id,entry_date,content)
      VALUES (:u,:d,:c)
      ON CONFLICT (user_id,entry_date)
      DO UPDATE SET content=:c"
    )->execute(["u"=>$user,"d"=>$date,"c"=>$_POST["content"]]);
}
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-primary text-white">
<div class="container mt-4">
<h3>Diary — <?= $date ?></h3>

<form method="POST">
<textarea name="content" class="form-control mb-3" rows="6"><?= htmlspecialchars($content) ?></textarea>
<button class="btn btn-light">Save</button>
</form>

<a href="dashboard.php" class="btn btn-light mt-3">⬅ Back</a>
</div>
</body>
</html>
