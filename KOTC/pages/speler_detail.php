<?php
require_once '../config/db_connection.php';
require_once '../includes/admin_functions.php';

// Haal speler ID uit URL
$playerId = $_GET['id'] ?? null;
$player = null;
$playerMatches = [];

if ($playerId) {
    // Haal speler info
    $query = "SELECT id, naam, leeftijd, geslacht, speelniveau, email, telefoon FROM spelers WHERE id = :id";
    $stmt = $PDO->prepare($query);
    $stmt->execute([':id' => $playerId]);
    $player = $stmt->fetch();
    
    if ($player) {
        // Haal matches via bestaande functie
        $playerMatches = getPlayerMatches($PDO, $playerId);
    }
}

// Groepeer matches per ronde
$matchesByRonde = [];
if ($player && !empty($playerMatches)) {
    foreach ($playerMatches as $match) {
        $ronde = $match['ronde'];
        if (!isset($matchesByRonde[$ronde])) {
            $matchesByRonde[$ronde] = [];
        }
        $matchesByRonde[$ronde][] = $match;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $player ? htmlspecialchars($player['naam']) : 'Speler'; ?> - King of the Courtz</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        .playerDetailHeader {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
        }
        .playerInfo {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .infoField {
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            border-left: 3px solid #007bff;
        }
        .infoLabel {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }
        .infoValue {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 0.25rem;
            color: #333;
        }
        .scheduleSection h3 {
            margin-top: 2rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }
        .rondeGroup {
            margin-bottom: 1.5rem;
        }
        .rondeGroup h4 {
            background: #e9ecef;
            padding: 0.75rem;
            border-radius: 0.25rem;
            margin: 0 0 0.75rem 0;
            font-size: 0.95rem;
        }
        .scheduleCard {
            background: white;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }
        .matchTime {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .matchStatus {
            display: inline-block;
            margin-left: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .matchStatus.gepland {
            background: #ffc107;
            color: #333;
        }
        .matchStatus.afgerond {
            background: #28a745;
            color: white;
        }
        .backLink {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background 0.3s;
        }
        .backLink:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <header>
        <a href="../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="registratie.php">Inschrijving</a></button>
            <button><a href="schema_all.php">Schema</a></button>
            <button><a href="beheer_spelers.php">Deelnemers</a></button>
            <button><a href="admin/login.php">Admin</a></button>
        </nav>
    </header>

    <div class="container">
        <?php if (!$player): ?>
            <div class="pageShell pageCard">
                <a href="beheer_spelers.php" class="backLink">← Terug naar Deelnemers</a>
                <p style="color: #dc3545;">Speler niet gevonden (ID: <?= htmlspecialchars($playerId) ?>)</p>
            </div>
        <?php else: ?>
            <div class="pageShell">
                <a href="beheer_spelers.php" class="backLink">← Terug naar Deelnemers</a>

                <!-- Speler Profiel -->
                <div class="playerDetailHeader pageCard">
                    <h1 style="margin: 0 0 1rem 0;"><?= htmlspecialchars($player['naam']) ?></h1>
                    <div class="playerInfo">
                        <div class="infoField">
                            <div class="infoLabel">Leeftijd</div>
                            <div class="infoValue"><?= htmlspecialchars($player['leeftijd']) ?></div>
                        </div>
                        <div class="infoField">
                            <div class="infoLabel">Geslacht</div>
                            <div class="infoValue"><?= ucfirst(htmlspecialchars($player['geslacht'])) ?></div>
                        </div>
                        <div class="infoField">
                            <div class="infoLabel">Speelniveau</div>
                            <div class="infoValue"><?= ucfirst(htmlspecialchars($player['speelniveau'])) ?></div>
                        </div>
                        <div class="infoField">
                            <div class="infoLabel">Email</div>
                            <div class="infoValue" style="font-size: 0.9rem; word-break: break-all;"><?= htmlspecialchars($player['email'] ?? '-') ?></div>
                        </div>
                        <div class="infoField">
                            <div class="infoLabel">Telefoon</div>
                            <div class="infoValue"><?= htmlspecialchars($player['telefoon'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Wedstrijdschema -->
                <div class="scheduleSection pageCard">
                    <h3>Wedstrijdschema</h3>

                    <?php if (empty($playerMatches)): ?>
                        <p style="color: #dc3545; font-weight: 600;">Geen wedstrijden gepland voor deze speler.</p>
                    <?php else: ?>
                        <?php foreach ($matchesByRonde as $ronde => $matches): ?>
                            <div class="rondeGroup">
                                <h4>Ronde <?= $ronde ?> • <?= getRondeTime($ronde) ?></h4>
                                <?php foreach ($matches as $match): ?>
                                    <?php
                                    // Bepaal welk team deze speler in zit
                                    $isTeam1Speler1 = $match['team1_speler1'] == $playerId;
                                    $isTeam1Speler2 = $match['team1_speler2'] == $playerId;
                                    
                                    if ($isTeam1Speler1 || $isTeam1Speler2) {
                                        $partner = ($isTeam1Speler1) ? $match['team1_speler2_naam'] : $match['team1_speler1_naam'];
                                        $opp1 = $match['team2_speler1_naam'];
                                        $opp2 = $match['team2_speler2_naam'];
                                    } else {
                                        $partner = ($match['team2_speler1'] == $playerId) ? $match['team2_speler2_naam'] : $match['team2_speler1_naam'];
                                        $opp1 = $match['team1_speler1_naam'];
                                        $opp2 = $match['team1_speler2_naam'];
                                    }
                                    ?>
                                    <div class="scheduleCard">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                            <span class="matchTime">Baan <?= $match['baan'] ?></span>
                                            <span class="matchStatus <?= $match['status'] ?>">
                                                <?= $match['status'] === 'gepland' ? '● Gepland' : '✓ Gespeeld' ?>
                                            </span>
                                        </div>
                                        <p style="margin: 0.5rem 0; font-weight: 600;">
                                            Partner: <span style="color: #007bff;"><?= htmlspecialchars($partner) ?></span>
                                        </p>
                                        <p style="margin: 0.25rem 0;">
                                            vs <strong><?= htmlspecialchars($opp1 . ' & ' . $opp2) ?></strong>
                                        </p>
                                        <?php if ($match['status'] === 'afgerond'): ?>
                                            <p style="margin: 0.25rem 0; color: #28a745; font-weight: 600;">
                                                Score: <?= $match['team1_score'] ?> - <?= $match['team2_score'] ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        <?php endif; ?>
    </div>

    <nav class="mobileBottomNav">
        <a href="../index.php">Home</a>
        <a href="schema_all.php">Schema</a>
        <a href="beheer_spelers.php">Spelers</a>
    </nav>
</body>
</html>
