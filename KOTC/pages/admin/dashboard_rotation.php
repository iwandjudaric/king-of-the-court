<?php
    session_start();
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
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'setup_rotation') {
        if (setupInitialRotation($PDO)) {
            // Generate matches voor beide groepen, ronde 1
            generateRoundMatches($PDO, 1, 1);
            generateRoundMatches($PDO, 1, 2);
            $message = 'Rotatie-systeem succesvol opgezet! Alle groepen Ronde 1 is klaar.';
        } else {
            $error = 'Fout bij setup. Zorg dat minstens 48 spelers zijn ingeschreven.';
        }
    } elseif ($_POST['action'] === 'process_and_rotate') {
        $ronde = intval($_POST['ronde'] ?? 1);
        $groep = intval($_POST['groep'] ?? 1);
        
        if (processRoundResults($PDO, $ronde, $groep)) {
            // Generate matches voor volgende ronde
            $nextRonde = $ronde + 1;
            
            if ($nextRonde <= NUM_RONDES) {
                generateRoundMatches($PDO, $nextRonde, $groep);
                $message = "Resultaten verwerkt! Groep $groep, Ronde $nextRonde is klaar om te spelen.";
            } else {
                // Check of volgende groep nog speelt
                if ($groep < NUM_GROEPEN) {
                    $nextGroep = $groep + 1;
                    generateRoundMatches($PDO, 1, $nextGroep);
                    $message = "Groep $groep is klaar! Groep $nextGroep, Ronde 1 begint nu.";
                } else {
                    // Toernooi voorbij - finale
                    $message = "Toernooi is voorbij! Check de King of the Court finalists.";
                }
            }
            
            // Update status
            $PDO->exec("UPDATE toernooi_status SET huidige_ronde = $nextRonde, huidige_groep = $groep");
        } else {
            $error = 'Fout bij verwerken van resultaten.';
        }
    } elseif ($_POST['action'] === 'logout') {
        logoutAdmin();
        header('Location: login.php');
        exit();
    }
}

// Haal huidige ronde/groep op
$huidigeRonde = $status['huidige_ronde'] ?? 0;
$huidigeGroep = $status['huidige_groep'] ?? 0;

// Haal huidge matches op (groep 1 ronde 1 als voorbeeld)
$displayRonde = $huidigeRonde > 0 ? $huidigeRonde : 1;
$displayGroep = $huidigeGroep > 0 ? $huidigeGroep : 1;
$huidigeMatches = getRondeResultaten($PDO, $displayRonde, $displayGroep);
$huidigeSetup = getRondeSetup($PDO, $displayRonde, $displayGroep);

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - King of the Courtz</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        .rotation-status {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .rotation-status strong { color: #007bff; }
        .match-result-input {
            margin: 10px 0;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .match-result-input label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .match-result-input input {
            width: 80px;
            padding: 5px;
            margin-right: 10px;
        }
        .btn-action { 
            background-color: #007bff; 
            color: white; 
            padding: 8px 15px; 
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        .btn-action:hover { background-color: #0056b3; }
        .match-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .match-card {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .match-card h4 { margin: 0 0 10px 0; color: #333; }
        .team {
            background: #f5f5f5;
            padding: 8px;
            margin: 5px 0;
            border-radius: 3px;
        }
    </style>
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
            <h1>Admin Dashboard - Rotatie Systeem</h1>
            
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
            
            <?php if (!empty($error)): ?>
                <div class="message" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin: 10px 0;">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <h2>Toernooistatus</h2>
                <p><strong>Ingeschreven spelers:</strong> <?= $playerCount ?> / 48</p>
                <p><strong>Status:</strong> <span style="color: <?= ($status && $status['loting_klaar']) ? '#28a745' : '#ffc107' ?>; font-weight: bold;"><?= ($status && $status['loting_klaar']) ? 'GESTART' : 'NIET GESTART' ?></span></p>
                <?php if ($status && $status['loting_klaar']): ?>
                    <div class="rotation-status">
                        <p><strong>Huidge ronde:</strong> <?= $huidigeRonde ?>/6</p>
                        <p><strong>Huidge groep:</strong> <?= $huidigeGroep ?>/2</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="info-box">
                <h2>Acties</h2>
                
                <?php if ($playerCount >= 48 && !($status && $status['loting_klaar'])): ?>
                    <p>Klaar om het toernooi te starten met <?= $playerCount ?> inschrijvingen.</p>
                    <form method="POST" action="">
                        <button type="submit" name="action" value="setup_rotation" class="btn-submit">Rotatie-Systeem Starten</button>
                    </form>
                <?php elseif ($playerCount < 48): ?>
                    <p style="color: #ffc107; font-weight: bold;">Wacht tot minstens 48 spelers zich hebben ingeschreven.</p>
                    <p>Huidge inschrijvingen: <?= $playerCount ?> / 48</p>
                <?php endif; ?>
            </div>

            <?php if ($status && $status['loting_klaar']): ?>
                <div class="info-box">
                    <h2>Huidge Wedstrijden - Groep <?= $displayGroep ?>, Ronde <?= $displayRonde ?></h2>
                    
                    <?php if (count($huidigeMatches) > 0): ?>
                        <div class="match-list">
                            <?php foreach ($huidigeMatches as $match): 
                                // Haal speelsnamen op
                                $t1s1Query = "SELECT naam FROM spelers WHERE id = " . $match['team1_speler1'];
                                $t1s1 = $PDO->query($t1s1Query)->fetch()['naam'];
                                
                                $t1s2Query = "SELECT naam FROM spelers WHERE id = " . $match['team1_speler2'];
                                $t1s2 = $PDO->query($t1s2Query)->fetch()['naam'];
                                
                                $t2s1Query = "SELECT naam FROM spelers WHERE id = " . $match['team2_speler1'];
                                $t2s1 = $PDO->query($t2s1Query)->fetch()['naam'];
                                
                                $t2s2Query = "SELECT naam FROM spelers WHERE id = " . $match['team2_speler2'];
                                $t2s2 = $PDO->query($t2s2Query)->fetch()['naam'];
                            ?>
                                <div class="match-card">
                                    <h4>Baan <?= $match['baan'] ?></h4>
                                    <div class="team">
                                        <strong>Team 1:</strong> <?= htmlspecialchars($t1s1) ?> + <?= htmlspecialchars($t1s2) ?>
                                    </div>
                                    <div class="team">
                                        <strong>Team 2:</strong> <?= htmlspecialchars($t2s1) ?> + <?= htmlspecialchars($t2s2) ?>
                                    </div>
                                    
                                    <?php if ($match['status'] === 'gepland'): ?>
                                        <form method="POST" action="update_result.php" style="margin-top: 10px;">
                                            <input type="hidden" name="ronde" value="<?= $displayRonde ?>">
                                            <input type="hidden" name="groep" value="<?= $displayGroep ?>">
                                            <input type="hidden" name="baan" value="<?= $match['baan'] ?>">
                                            
                                            <label>Team 1 Score: <input type="number" name="score_team1" min="0" max="99" required></label>
                                            <label>Team 2 Score: <input type="number" name="score_team2" min="0" max="99" required></label>
                                            <button type="submit" class="btn-action">Resultaat Opslaan</button>
                                        </form>
                                    <?php else: ?>
                                        <p style="color: #28a745; font-weight: bold;">✓ Afgerond</p>
                                        <p>Team 1: <?= $match['score_team1'] ?> | Team 2: <?= $match['score_team2'] ?></p>
                                        <p style="color: #007bff;">Winnaar: Team <?= $match['winner'] ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php 
                        // Check als alle matches afgerond zijn
                        $allCompleted = count(array_filter($huidigeMatches, fn($m) => $m['status'] === 'afgerond')) === count($huidigeMatches);
                        if ($allCompleted):
                        ?>
                            <form method="POST" action="" style="margin-top: 20px;">
                                <input type="hidden" name="ronde" value="<?= $displayRonde ?>">
                                <input type="hidden" name="groep" value="<?= $displayGroep ?>">
                                <button type="submit" name="action" value="process_and_rotate" class="btn-submit">Resultaten Verwerken & Roteren</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Geen wedstrijden gepland.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="admin_only.php">← Terug naar Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
