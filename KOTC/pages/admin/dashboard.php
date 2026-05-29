<?php
    session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/player_functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$playerCount = getPlayerCount($PDO);
$status = getTournamentStatus($PDO);
$isClosed = isTournamentClosed($PDO);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'perform_draw') {
        if (performDraw($PDO)) {
            $message = 'Loting succesvol uitgevoerd!';
        } else {
            $message = 'Fout bij loting. Zorg dat minstens 48 spelers zijn ingeschreven.';
        }
    } elseif ($_POST['action'] === 'logout') {
        logoutAdmin();
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - King of the Courtz</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <header>
        <a href="../../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="../registratie.php">Inschrijving</a></button>
            <button><a href="../schema.php">Schema</a></button>
            <button><a href="../beheer_spelers.php">Deelnemers</a></button>
            <button><a href="login.php">Admin</a></button>
        </nav>
    </header>

    <div class="container">
        <div class="admin-wrapper">
            <h1>Admin Dashboard</h1>
            
            <div class="info-box">
                <p><strong>Welkom, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</strong></p>
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
                <h2>Toernooistatus</h2>
                <p><strong>Ingeschreven spelers:</strong> <?= $playerCount ?> / 50</p>
                <p><strong>Status:</strong> <span style="color: <?= $isClosed ? '#dc3545' : '#28a745' ?>; font-weight: bold;"><?= $isClosed ? 'GESLOTEN' : 'OPEN' ?></span></p>
                <p><strong>Loting:</strong> <span style="color: <?= ($status && $status['loting_klaar']) ? '#28a745' : '#ffc107' ?>; font-weight: bold;"><?= ($status && $status['loting_klaar']) ? 'GEDAAN' : 'NIET GEDAAN' ?></span></p>
            </div>
            
            <div class="info-box">
                <h2>Acties</h2>
                
                <?php if ($playerCount >= 48 && !($status && $status['loting_klaar'])): ?>
                    <p style="margin-bottom: 15px;">Er zijn genoeg spelers ingeschreven om de loting uit te voeren.</p>
                    <form method="POST" action="">
                        <button type="submit" name="action" value="perform_draw" class="btn-submit">Loting Uitvoeren</button>
                    </form>
                <?php elseif ($playerCount < 48): ?>
                    <p style="color: #ffc107; font-weight: bold;">Wacht tot minstens 48 spelers zich hebben ingeschreven voor loting.</p>
                    <p>Huidige inschrijvingen: <?= $playerCount ?> / 48</p>
                <?php else: ?>
                    <p style="color: #28a745; font-weight: bold;">Loting is al gedaan.</p>
                <?php endif; ?>
            </div>
            
            <div class="info-box">
                <h2>Beheerpagina's</h2>
                <div style="margin-top: 15px;">
                    <a href="admin_only.php" class="btn-link">Admin Panel (Uitgebreid)</a>
                    <a href="edit_players.php" class="btn-link">Spelers Bewerken</a>
                    <a href="edit_matches.php" class="btn-link">Wedstrijden Bewerken</a>
                </div>
            </div>
            
            <div class="back-link">
                <a href="../../index.php">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
