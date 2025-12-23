<?php
include "db.php";

$week=$_GET['week'] ?? 1;
$year=$_GET['year'];
$month=$_GET['month'];
$cat=$_GET['cat'];

$start=date('Y-m-d',strtotime("$year-$month-01 +".(($week-1)*7)." days"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Weekly Tasks</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="container py-5">

<div class="card p-4 shadow">

<h4><?= date('F',mktime(0,0,0,$month,1)) ?> – Week <?= $week ?></h4>

<div class="d-flex justify-content-between mb-3">
<a href="?cat=<?=$cat?>&year=<?=$year?>&month=<?=$month?>&week=<?=max(1,$week-1)?>">⬅️ Previous</a>
<a href="?cat=<?=$cat?>&year=<?=$year?>&month=<?=$month?>&week=<?=($week+1)?>">Next ➡️</a>
</div>

<table class="table table-bordered align-middle">
<tr>
<th>Date</th>
<th>What To Do</th>
<th>Status</th>
</tr>

<?php
for($i=0;$i<7;$i++){
$date=date('Y-m-d',strtotime("$start +$i days"));

$data=$conn->query("SELECT * FROM tasks 
WHERE user_id='{$_SESSION['user_id']}' 
AND task_date='$date' 
AND category_id='$cat'")->fetch_assoc();

$task=$data['task_description'] ?? '';
$status=$data['status'] ?? 'Pending';

echo "
<tr>
<td>$date</td>
<td>
<input class='form-control task-input'
data-date='$date'
value='$task'>
</td>
<td>
<select class='form-select status-select' data-date='$date'>
<option ".($status=='Pending'?'selected':'').">⏳ Pending</option>
<option ".($status=='In Progress'?'selected':'').">🚧 In Progress</option>
<option ".($status=='Done'?'selected':'').">✅ Done</option>
</select>
</td>
</tr>";
}
?>
</table>

<div id="saveMsg" class="text-success fw-bold"></div>

</div>

<script>
$('.task-input, .status-select').on('change keyup', function() {
    let row = $(this).closest('tr');
    let date = $(this).data('date');
    let task = row.find('.task-input').val();
    let status = row.find('.status-select').val();

    $.post('ajax_task.php', {
        date: date,
        task: task,
        status: status,
        category: <?= $cat ?>
    }, function() {
        $('#saveMsg').text('✔ Changes saved automatically');
        setTimeout(()=>$('#saveMsg').text(''),2000);
    });
});
</script>

</body>
</html>

