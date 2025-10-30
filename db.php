<?php
// Enkel PDO-anslutning — ändra användare/lösen om behövligt
$dsn = 'mysql:host=127.0.0.1;dbname=copilottesting;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB connection error: ' . $e->getMessage());
}
?>