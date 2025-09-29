<?php
session_start();

// Enkla hårdkodade användaruppgifter
$valid_username = "elgiganten";
$valid_password = "hemligt123";

if ($_POST["username"] === $valid_username && $_POST["password"] === $valid_password) {
    $_SESSION["logged_in"] = true;
    header("Location: welcome.php");
    exit;
} else {
    echo "<h3>Fel användarnamn eller lösenord</h3>";
    echo "<a href='login.php'>Försök igen</a>";
}
?>
