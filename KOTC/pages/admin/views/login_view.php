<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - King of the Courtz</title>
    <link rel="stylesheet" href="../../../public/css/style.css">
</head>
<body>
    <header>
        <a href="../../../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="../../registratie.php">Inschrijving</a></button>
            <button><a href="../../schema.php">Schema</a></button>
            <button><a href="../../beheer_spelers.php">Deelnemers</a></button>
            <button><a href="login.php">Admin</a></button>
        </nav>
    </header>

    <div class="pageShell login-page">
        <div class="pageCard" style="max-width: 400px; margin-left: auto; margin-right: auto;">
            <h1 class="pageTitle">Admin Login</h1>
            
            <?php if (!empty($error)): ?>
                <div class="message message-error">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Gebruikersnaam</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-submit">Inloggen</button>
            </form>
            
            <div class="info-box">
                <p><strong>Demo Account:</strong></p>
                <p>Gebruikersnaam: <code>admin</code></p>
                <p>Wachtwoord: <code>admin123</code></p>
            </div>
            
            <div class="back-link">
                <a href="../../../index.php">Terug naar home</a>
            </div>
        </div>
    </div>

    <nav class="mobileBottomNav">
        <a href="../../../index.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Home</span>
        </a>
        <a href="../../registratie.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            <span>Inschrijving</span>
        </a>
        <a href="../../schema.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4z"/></svg>
            <span>Schema</span>
        </a>
    </nav>
</body>
</html>
