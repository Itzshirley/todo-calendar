<?php
include "db.php";

$user_id = $_SESSION['user_id'];
$date = $_POST['date'];
$category = $_POST['category'];
$task = $_POST['task'];
$status = $_POST['status'];

// Check if task exists
$check = $conn->query("SELECT id FROM tasks 
WHERE user_id='$user_id' AND task_date='$date' AND category_id='$category'");

if ($check->num_rows > 0) {
    // Update
    $conn->query("UPDATE tasks SET 
    task_description='$task', status='$status'
    WHERE user_id='$user_id' AND task_date='$date' AND category_id='$category'");
} else {
    // Insert
    $conn->query("INSERT INTO tasks 
    (user_id, category_id, task_date, task_description, status)
    VALUES ('$user_id','$category','$date','$task','$status')");
}

echo "saved";
