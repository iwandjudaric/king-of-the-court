    <?php
require_once 'config/db_connection.php';
require_once 'includes/admin_functions.php';
require_once 'includes/player_functions.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>King of the Courtz - Padel Tournament</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <a href="index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="pages/registratie.php">Inschrijving</a></button>
            <button><a href="pages/schema_all.php">Schema</a></button>
            <button><a href="pages/beheer_spelers.php">Deelnemers</a></button>
            <button><a href="pages/admin/login.php">Admin</a></button>
        </nav>
    </header>

    <div class="container">
        <div class="home-wrapper">
            <h1>King of the Courtz</h1>
            <p class="subtitle">Padel Toernooi Management Systeem</p>
            
            <div class="menu">
                <div class="menu-item">
                    <a href="pages/registratie.php" class="menu-link">
                        <div class="menu-icon">[REG]</div>
                        <div class="menu-title">Inschrijving</div>
                        <div class="menu-desc">Schrijf jezelf in voor het toernooi</div>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="pages/schema_all.php" class="menu-link">
                        <div class="menu-icon">[SCH]</div>
                        <div class="menu-title">Schema</div>
                        <div class="menu-desc">Bekijk wedstrijdschema en uitslagen</div>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="pages/beheer_spelers.php" class="menu-link">
                        <div class="menu-icon">[PLY]</div>
                        <div class="menu-title">Deelnemers</div>
                        <div class="menu-desc">Bekijk alle ingeschreven spelers</div>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="pages/admin/login.php" class="menu-link">
                        <div class="menu-icon">[ADM]</div>
                        <div class="menu-title">Administratie</div>
                        <div class="menu-desc">Admin panel</div>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="pages/finales.php" class="menu-link">
                        <div class="menu-title">King of the Court</div>
                        <div class="menu-desc">Bekijk de finale resultaten</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <nav class="mobileBottomNav">
        <a href="index.php" class="mobileNavItem active">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Home</span>
        </a>
        <a href="pages/registratie.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            <span>Inschrijving</span>
        </a>
        <a href="pages/schema_all.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4z"/></svg>
            <span>Schema</span>
        </a>
    </nav>
</body>
</html>
