<?php
require "db.php";
session_start();

$userId = $_SESSION["user_id"];
$month = $_GET["month"];
$year  = $_GET["year"];

$first = strtotime("$year-$month-01");
$days  = date("t", $first);
$start = date("N", $first);
$today = date("Y-m-d");

$stmt = $pdo->prepare("
SELECT task_date,status FROM tasks
WHERE user_id=:u
AND EXTRACT(MONTH FROM task_date)=:m
AND EXTRACT(YEAR FROM task_date)=:y
");
$stmt->execute(["u"=>$userId,"m"=>$month,"y"=>$year]);

$map=[];
foreach($stmt as $r){ $map[$r["task_date"]][]=$r["status"]; }

for($i=1;$i<$start;$i++) echo "<div></div>";

for($d=1;$d<=$days;$d++){
$date=sprintf("%04d-%02d-%02d",$year,$month,$d);
$cls=$date==$today?"day today":"day";
$icons="";
if(isset($map[$date])){
foreach($map[$date] as $s){
$icons .= $s=="done"?"✅ ":($s=="in_progress"?"🚧 ":"⏳ ");
}}
echo "<div class='$cls' onclick=\"openDiary('$date')\">
<b>$d</b><br>$icons</div>";
}
