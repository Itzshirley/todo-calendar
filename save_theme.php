<?php
session_start();
require_once "db.php";

$theme = $_POST["theme"];
$user = $_SESSION["user_id"];

$pdo->prepare(
 "UPDATE users SET theme=:t WHERE id=:u"
)->execute(["t"=>$theme,"u"=>$user]);

$_SESSION["theme"] = $theme;
header("Location: dashboard.php");
