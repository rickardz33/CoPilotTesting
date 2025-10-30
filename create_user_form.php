<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Skapa användare</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 0 auto; padding: 0 1rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.5rem; }
        button { padding: 0.5rem 1rem; background: #333; color: white; border: none; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>
    
    <h1>Skapa ny användare</h1>
    
    <form action="create_user.php" method="post">
        <div class="form-group">
            <label for="username">Användarnamn:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Lösenord:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Skapa användare</button>
    </form>
</body>
</html>