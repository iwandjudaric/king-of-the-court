<?php
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/player_functions.php';
require_once '../../includes/rotation_functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$playerCount = getPlayerCount($PDO);
$status = getTournamentStatus($PDO);
$isClosed = isTournamentClosed($PDO);
$drawCompleted = $status && $status['loting_klaar'];

$allPlayers = getAllPlayers($PDO);
$normalPlayers = array_slice($allPlayers, 0, 48);
$reserves = array_slice($allPlayers, 48);

$allMatches = getAllMatches($PDO);
$completedMatches = array_filter($allMatches, function($m) { return $m['status'] === 'afgerond'; });
$pendingMatches = array_filter($allMatches, function($m) { return $m['status'] !== 'afgerond'; });

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'perform_draw') {
        if (performDraw($PDO)) {
            $message = 'Loting succesvol uitgevoerd!';
            $status = getTournamentStatus($PDO);
            $drawCompleted = $status && $status['loting_klaar'];
            $allMatches = getAllMatches($PDO);
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
    <title>Admin Panel - King of the Courtz</title>
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
            <h1>Admin Panel (Uitgebreid)</h1>
            
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
            
            <div class="info-box" style="background-color: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px;">
                <h2 style="color: #856404; margin-top: 0;">⚠️ Nieuw Systeem Actief</h2>
                <p style="color: #856404; margin-bottom: 15px;"><strong>Het rotatie-systeem is nu actief. Alle toernooibeheer gebeurt via het nieuwe dashboard.</strong></p>
                <a href="dashboard_rotation.php" class="btn-submit" style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;">🚀 Ga naar Rotatie Dashboard</a>
            </div>
            
            <div class="info-box">
                <h2>Beheerpagina's</h2>
                <div style="margin-top: 15px;">
                    <a href="dashboard_rotation.php" class="btn-link">🎯 Rotatie-Systeem Dashboard</a>
                    <a href="edit_players.php" class="btn-link">Spelers Bewerken</a>
                    <a href="edit_matches.php" class="btn-link">Wedstrijden Bewerken</a>
                    <a href="dashboard.php" class="btn-link">Terug naar Dashboard</a>
                </div>
            </div>
            
            <div class="back-link">
                <a href="../../index.php">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
