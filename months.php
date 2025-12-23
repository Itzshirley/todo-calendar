<?php
$cat=$_GET['cat'];
$year=$_GET['year'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Months</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="container py-5">
<div class="card p-4 shadow">
<h4><?= $year ?> – Select Month</h4>

<div class="vertical-list">
<?php
for ($m=1;$m<=12;$m++){
    $name=date('F',mktime(0,0,0,$m,1));
    echo "<a href='calendar.php?cat=$cat&year=$year&month=$m'>
          <button class='btn btn-outline-primary'>$name</button></a>";
}
?>
</div>
</div>
</body>
</html>
