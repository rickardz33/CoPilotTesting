<?php if(!isset($_SESSION)) { session_start(); } ?>
<style>
    .main-nav { background: #333; color: white; width: 100%; margin-bottom: 2rem; }
    .nav-container { max-width: 720px; margin: 0 auto; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
    .nav-links { display: flex; gap: 1rem; align-items: center; }

    /* Gör länkar vita */
    .main-nav .nav-links a,
    .main-nav .nav-links span,
    .main-nav .brand { color: white; }

    /* Hamburgermeny */
    .hamburger {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .menu-mobile {
        display: none;
        position: absolute;
        top: 60px;
        right: 1rem;
        background: #444;
        border-radius: 4px;
        min-width: 200px;
        z-index: 1000;
    }

    .menu-mobile.active {
        display: flex;
        flex-direction: column;
    }

    .menu-mobile a,
    .menu-mobile button {
        color: white;
        text-decoration: none;
        padding: 0.75rem 1rem;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
        width: 100%;
    }

    .menu-mobile a:hover,
    .menu-mobile button:hover {
        background: #555;
    }

    /* Knapp-stil */
    .nav-btn,
    .logout-btn {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        background: #666;
        border-radius: 4px;
        display: none;
    }
    .nav-btn:hover,
    .logout-btn:hover { background: #777; }

    /* Responsive */
    @media (max-width: 600px) {
        .hamburger {
            display: block;
        }
        .nav-links {
            display: none;
        }
    }

    @media (min-width: 601px) {
        .hamburger {
            display: none;
        }
        .nav-links {
            display: flex !important;
        }
        .menu-mobile {
            display: none !important;
        }
    }
</style>

<nav class="main-nav">
    <div class="nav-container">
        <span class="brand">Min Todo-App</span>
        
        <!-- Desktop meny -->
        <div class="nav-links">
            <?php if(isset($_SESSION["logged_in"])): ?>
                <span>Inloggad</span>
                <a href="create_user_form.php" class="nav-btn">Skapa användare</a>
                <a href="logout.php" class="logout-btn">Logga ut</a>
            <?php endif; ?>
        </div>

        <!-- Hamburgermeny knapp -->
        <button class="hamburger" onclick="toggleMenu()">☰</button>
    </div>

    <!-- Mobil meny -->
    <div class="menu-mobile" id="mobileMenu">
        <?php if(isset($_SESSION["logged_in"])): ?>
            <a href="create_user_form.php">Skapa användare</a>
            <a href="logout.php">Logga ut</a>
        <?php endif; ?>
    </div>
</nav>

<script>
function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('active');
}

// Stäng meny när man klickar utanför
document.addEventListener('click', function(event) {
    const menu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger');
    if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
        menu.classList.remove('active');
    }
});
</script>