<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - King of the Courtz</title>
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

    <div class="pageShell edit-page">
        <div class="pageCard">
            <h1 class="pageTitle">Admin Panel (Uitgebreid)</h1>
            
            <div class="info-box">
                <p><strong>Ingelogd als: <?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
                <form method="POST" action="" style="display: inline;">
                    <button type="submit" name="action" value="logout" class="btn-delete" style="margin-top: 10px;">Uitloggen</button>
                </form>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message message-success">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <h2>Toernooigegevens</h2>
                <p><strong>Totale inschrijvingen:</strong> <?= $playerCount ?> / 50</p>
                <p><strong>Normale deelnemers:</strong> <?= count($normalPlayers) ?></p>
                <p><strong>Reservespelers:</strong> <?= count($reserves) ?></p>
                <p><strong>Status:</strong> <span style="color: <?= $isClosed ? '#dc3545' : '#28a745' ?>; font-weight: bold;"><?= $isClosed ? 'GESLOTEN' : 'OPEN' ?></span></p>
                <p><strong>Loting:</strong> <span style="color: <?= $drawCompleted ? '#28a745' : '#ffc107' ?>; font-weight: bold;"><?= $drawCompleted ? 'GEDAAN' : 'NIET GEDAAN' ?></span></p>
            </div>
            
            <div class="info-box">
                <h2>Toernooibeheer</h2>
                
                <?php if (!$drawCompleted && $playerCount >= 48): ?>
                    <p style="margin-bottom: 15px;">Er zijn genoeg spelers ingeschreven om de loting uit te voeren.</p>
                    <form method="POST" action="">
                        <button type="submit" name="action" value="perform_draw" class="btn-submit">Loting Uitvoeren</button>
                    </form>
                <?php elseif ($playerCount < 48): ?>
                    <p style="color: #ffc107; font-weight: bold;">Wacht tot minstens 48 spelers zich hebben ingeschreven.</p>
                    <p>Huidige inschrijvingen: <?= $playerCount ?> / 48</p>
                <?php else: ?>
                    <p style="color: #28a745; font-weight: bold;">Loting is al gedaan.</p>
                <?php endif; ?>
            </div>
            
            <?php if ($drawCompleted): ?>
                <div class="info-box">
                    <h2>Wedstrijdstatus</h2>
                    <p><strong>Geplande wedstrijden:</strong> <?= count($pendingMatches) ?></p>
                    <p><strong>Afgeronde wedstrijden:</strong> <?= count($completedMatches) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <h2>Beheerpagina's</h2>
                <div style="margin-top: 15px;">
                    <a href="edit_players.php" class="btn-link">Spelers Bewerken</a>
                    <a href="edit_matches.php" class="btn-link">Wedstrijden Bewerken</a>
                    <a href="dashboard.php" class="btn-link">Terug naar Dashboard</a>
                </div>
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
    </nav></body>
</html>
