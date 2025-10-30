<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = :username');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION["logged_in"] = true;
            $_SESSION["user_id"] = $user['id'];
            header("Location: welcome.php");
            exit;
        } else {
            echo "<h3>Fel användarnamn eller lösenord</h3>";
            echo "<a href='login.php'>Försök igen</a>";
        }
    } catch (PDOException $e) {
        // Logga felet säkert, visa användarvänligt meddelande
        error_log($e->getMessage());
        echo "<h3>Ett fel uppstod</h3>";
        echo "<a href='login.php'>Försök igen</a>";
    }
}
?>
