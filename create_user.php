<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password
        ]);
        header("Location: welcome.php?message=user_created");
        exit;
    } catch (PDOException $e) {
        header("Location: create_user_form.php?error=1");
        exit;
    }
}
?>
<style>
.nav-btn { 
    color: white; 
    text-decoration: none; 
    padding: 0.5rem 1rem; 
    background: #666; 
    border-radius: 4px; 
}
.nav-btn:hover { 
    background: #777; 
}
</style>