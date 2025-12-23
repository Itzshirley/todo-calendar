<?php
$host = "db.abcdxyz.supabase.co";
$db   = "postgres";
$user = "postgres";
$pass = "YOUR_PASSWORD";
$port = "5432";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>

