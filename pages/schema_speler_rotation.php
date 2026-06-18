<?php
    session_start();
require_once '../config/db_connection.php';
require_once '../includes/admin_functions.php';
require_once '../includes/player_functions.php';
require_once '../includes/rotation_functions.php';

$speler_id = intval($_GET['speler_id'] ?? 0);

if ($speler_id <= 0) {
    header('Location: schema_all.php');
    exit();
}

// Haal speler info
$query = "SELECT id, naam, leeftijd, geslacht, speelniveau FROM spelers WHERE id = :id";
$stmt = $PDO->prepare($query);
$stmt->execute([':id' => $speler_id]);
$speler = $stmt->fetch();

if (!$speler) {
    die('Speler niet gevonden.');
}

// Haal speler's schema voor alle rondes
$schedule = [];
try {
    for ($r = 1; $r <= NUM_RONDES; $r++) {
        for ($g = 1; $g <= NUM_GROEPEN; $g++) {
            $query = "SELECT * FROM ronde_setup 
                     WHERE speler_id = :id AND ronde = :ronde AND groep = :groep LIMIT 1";
            $stmt = $PDO->prepare($query);
            $stmt->execute([':id' => $speler_id, ':ronde' => $r, ':groep' => $g]);
            $pos = $stmt->fetch();
            
            if ($pos) {
                // Haal partner info op direct uit ronde_setup
                $partner_id = $pos['partner_speler_id'] ?? null;
                $partner_naam = 'Onbekend';
                
                if ($partner_id) {
                    $pq = "SELECT naam FROM spelers WHERE id = :id";
                    $ps = $PDO->prepare($pq);
                    $ps->execute([':id' => $partner_id]);
                    $pdata = $ps->fetch();
                    $partner_naam = $pdata ? $pdata['naam'] : 'Onbekend';
                }
                
                // Haal match op voor deze baan/ronde/groep
                $mqry = "SELECT * FROM ronde_resultaten 
                        WHERE ronde = :ronde AND groep = :groep AND baan = :baan LIMIT 1";
                $mstmt = $PDO->prepare($mqry);
                $mstmt->execute([':ronde' => $r, ':groep' => $g, ':baan' => $pos['baan']]);
                $match = $mstmt->fetch();
                
                if ($match) {
                    // Bepaal team van speler
                    $isTeam1 = ($match['team1_speler1'] == $speler_id || $match['team1_speler2'] == $speler_id);
                    
                    // Haal alle speelsinformatie op met veilige queries
                    $spelerIds = [$match['team1_speler1'] ?? null, $match['team1_speler2'] ?? null, $match['team2_speler1'] ?? null, $match['team2_speler2'] ?? null];
                    $speelersData = [];
                    
                    foreach ($spelerIds as $id) {
                        if ($id) {
                            $sq = "SELECT id, naam FROM spelers WHERE id = :id";
                            $ss = $PDO->prepare($sq);
                            $ss->execute([':id' => $id]);
                            $s = $ss->fetch();
                            if ($s) {
                                $speelersData[$id] = $s['naam'];
                            }
                        }
                    }
                    
                    $s1n = $speelersData[$match['team1_speler1']] ?? 'Onbekend';
                    $s2n = $speelersData[$match['team1_speler2']] ?? 'Onbekend';
                    $s3n = $speelersData[$match['team2_speler1']] ?? 'Onbekend';
                    $s4n = $speelersData[$match['team2_speler2']] ?? 'Onbekend';
                    
                    // Tegenstandersinfo
                    $opponents = [];
                    if ($isTeam1) {
                        $opponents = [
                            ['naam' => $s3n, 'id' => $match['team2_speler1'] ?? null],
                            ['naam' => $s4n, 'id' => $match['team2_speler2'] ?? null]
                        ];
                    } else {
                        $opponents = [
                            ['naam' => $s1n, 'id' => $match['team1_speler1'] ?? null],
                            ['naam' => $s2n, 'id' => $match['team1_speler2'] ?? null]
                        ];
                    }
                    
                    $schedule[] = [
                        'ronde' => $r,
                        'groep' => $g,
                        'baan' => $pos['baan'],
                        'team' => $isTeam1 ? 1 : 2,
                        'partner_id' => $partner_id,
                        'partner_naam' => $partner_naam,
                        'opponents' => $opponents,
                        'match' => $match,
                        'tijd' => getRondeTime($r)
                    ];
                }
            }
        }
    }
} catch (Exception $e) {
    error_log("Schema error: " . $e->getMessage());
}

// Haal speler stats
$stats = getPlayerStats($PDO, $speler_id) ?? ['wins' => 0, 'losses' => 0, 'winrate' => 0];

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schema - <?= htmlspecialchars($speler['naam']) ?> - King of the Courtz</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        .speler-header {
            background: linear-gradient(135deg, #E8500A 0%, #F27B3F 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .speler-header h1 { margin: 0; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .stat-box strong { color: #E8500A; font-size: 1.5em; }
        .stat-box p { margin: 5px 0 0 0; color: #666; }
        .schedule-item {
            background: white;
            border-left: 4px solid #E8500A;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .schedule-item h3 { margin: 0 0 10px 0; color: #333; }
        .schedule-time {
            display: inline-block;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            color: #E8500A;
            margin-bottom: 10px;
        }
        .team-info {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        .result {
            background: #e8f5e9;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
            color: #2e7d32;
        }
        .result.loss {
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <header>
        <a href="../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="../registratie.php">Inschrijving</a></button>
            <button><a href="../schema.php">Schema</a></button>
            <button><a href="../beheer_spelers.php">Deelnemers</a></button>
            <button><a href="../admin/login.php">Admin</a></button>
        </nav>
    </header>

    <div class="container">
        <div class="speler-header">
            <h1><?= htmlspecialchars($speler['naam']) ?></h1>
            <p><strong>Leeftijd:</strong> <?= $speler['leeftijd'] ?> | 
               <strong>Geslacht:</strong> <?= $speler['geslacht'] ?> | 
               <strong>Niveau:</strong> <?= ucfirst($speler['speelniveau']) ?></p>
            
            <div class="stats-grid">
                <div class="stat-box">
                    <strong><?= $stats['wins'] ?? 0 ?></strong>
                    <p>Wins</p>
                </div>
                <div class="stat-box">
                    <strong><?= $stats['losses'] ?? 0 ?></strong>
                    <p>Losses</p>
                </div>
                <div class="stat-box">
                    <strong><?= round($stats['winrate'] ?? 0) ?>%</strong>
                    <p>Winrate</p>
                </div>
            </div>
        </div>

        <h2>Mijn Schema</h2>

        <?php if (count($schedule) > 0): ?>
            <?php foreach ($schedule as $item): ?>
                <div class="schedule-item">
                    <h3>Groep <?= $item['groep'] ?> - Ronde <?= $item['ronde'] ?></h3>
                    <div class="schedule-time"> <?= $item['tijd'] ?> - Baan <?= $item['baan'] ?></div>

                    <div class="team-info">
                        <strong>Mijn Team (Team <?= $item['team'] ?>):</strong>
                        <p><?= htmlspecialchars($speler['naam']) ?> + 
                           <?php if ($item['partner_id']): ?>
                               <a href="schema_speler_rotation.php?speler_id=<?= $item['partner_id'] ?>" style="color: #667eea;">
                                   <?= htmlspecialchars($item['partner_naam']) ?>
                               </a>
                           <?php else: ?>
                               <span><?= htmlspecialchars($item['partner_naam']) ?></span>
                           <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="team-info">
                        <strong>Tegenstanders (Team <?= $item['team'] == 1 ? 2 : 1 ?>):</strong>
                        <p>
                            <?php if ($item['opponents'][0]['id']): ?>
                                <a href="schema_speler_rotation.php?speler_id=<?= $item['opponents'][0]['id'] ?>" style="color: #764ba2;">
                                    <?= htmlspecialchars($item['opponents'][0]['naam']) ?>
                                </a>
                            <?php else: ?>
                                <span><?= htmlspecialchars($item['opponents'][0]['naam']) ?></span>
                            <?php endif; ?>
                            +
                            <?php if ($item['opponents'][1]['id']): ?>
                                <a href="schema_speler_rotation.php?speler_id=<?= $item['opponents'][1]['id'] ?>" style="color: #764ba2;">
                                    <?= htmlspecialchars($item['opponents'][1]['naam']) ?>
                                </a>
                            <?php else: ?>
                                <span><?= htmlspecialchars($item['opponents'][1]['naam']) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <?php if ($item['match'] && $item['match']['status'] === 'afgerond'): ?>
                        <div class="result <?= ($item['team'] == ($item['match']['winner'] ?? null)) ? '' : 'loss' ?>">
                            <strong>Resultaat:</strong> 
                            Team <?= $item['match']['winner'] ?? '?' ?> wint 
                            (<?= $item['match']['score_team1'] ?? '?' ?> - <?= $item['match']['score_team2'] ?? '?' ?>)
                        </div>
                    <?php else: ?>
                        <div style="color: #ffc107; padding: 10px; background: #fff3cd; border-radius: 3px;">
                            <strong>Status:</strong> Nog te spelen
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #999;">Geen wedstrijden ingepland.</p>
        <?php endif; ?>

        <hr style="margin: 40px 0;">
        
        <a href="schema_all.php" class="btn-link">← Terug naar Speelerslijst</a>
    </div>
</body>
</html>
