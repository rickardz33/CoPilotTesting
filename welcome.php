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
   <a href="https://elgiganten.eu.qlikcloud.com/sense/app/376a5db8-f215-458a-b29f-0f9b54fd220e/sheet/10937a23-edbb-4916-ac1d-ca36d20b46ec/state/analysis">LÄNK</a>
   
</body>
</html>
