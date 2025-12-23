<?php
include "db.php";

$user = $_SESSION['user_id'];
$date = $_POST['date'];
$task = $_POST['task'];
$status = $_POST['status'];
$category = $_POST['category'];

$conn->query("INSERT INTO tasks (user_id,category_id,task_date,task_description,status)
VALUES ('$user','$category','$date','$task','$status')");

header("Location: ".$_SERVER['HTTP_REFERER']);
