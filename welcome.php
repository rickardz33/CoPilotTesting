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
    <title>Välkommen</title>
</head>
<body>
    <h1>Hej Elgiganten!</h1>
    <p>Du är nu inloggad.</p>
</body>
</html>
