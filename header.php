<?php if(!isset($_SESSION)) { session_start(); } ?>
<nav class="main-nav">
    <div class="nav-container">
        <span class="brand">Min Todo-App</span>
        <div class="nav-links">
            <?php if(isset($_SESSION["logged_in"])): ?>
                <span>Inloggad</span>
                <a href="create_user_form.php" class="nav-btn">Skapa användare</a>
                <a href="logout.php" class="logout-btn">Logga ut</a>
            <?php endif; ?>
        </div>
    </div>
</nav>