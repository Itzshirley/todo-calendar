<?php include "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="container py-5">
<div class="card p-4 shadow">

<h4 class="text-center mb-4">Choose Category</h4>

<div class="column g-3">
<div class="col-md-4"><a href="years.php?cat=1" class="btn btn-blue w-100">💼 Work</a></div>
<div class="col-md-4"><a href="years.php?cat=2" class="btn btn-blue w-100">📚 School</a></div>
<div class="col-md-4"><a href="years.php?cat=3" class="btn btn-blue w-100">❤️ Health</a></div>
<div class="col-md-6"><a href="years.php?cat=4" class="btn btn-blue w-100">🏋️ Fitness</a></div>
<div class="col-md-6"><a href="years.php?cat=5" class="btn btn-blue w-100">📌 Others</a></div>
</div>

</div>
</body>
</html>
