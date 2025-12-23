<?php $cat=$_GET['cat']; ?>
<!DOCTYPE html>
<html>
<head>
<title>Years</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="container py-5">
<div class="card p-4 shadow">
<h4>Select Year</h4>

<div class="vertical-list">
<?php
for ($y=2025; $y<=2035; $y++) {
    echo "<a href='months.php?cat=$cat&year=$y'>
          <button class='btn btn-outline-primary'>$y</button></a>";
}
?>
</div>
</div>
</body>
</html>
