<?php
    session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/rotation_functions.php';

// Haal finale-kandidaten op (baan 1 winnaars na 6 rondes)
$finales = getKingOfCourtCandidates($PDO);

// Bepaal the ultimate King of the Court (de twee gegenmaren in ronde 6, baan 1)
$groep1_kotc = null;
$groep2_kotc = null;

foreach ($finales as $f) {
    if ($f['groep'] == 1) {
        $groep1_kotc = $f;
    } elseif ($f['groep'] == 2) {
        $groep2_kotc = $f;
    }
}

// Dus uiteindelijk: wie van G1 baan1 vs wie van G2 baan1?
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>King of the Court - Finales - King of the Courtz</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        .finals-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .finals-container h1 {
            margin: 0;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .finals-container p {
            margin: 10px 0;
            font-size: 1.2em;
        }
        
        .group-finalists {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
        }
        
        .group-finalists h2 {
            margin-top: 0;
            color: #667eea;
        }
        
        .finalist-pair {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .finalist-pair strong {
            color: #764ba2;
            font-size: 1.1em;
        }
        
        .vs-text {
            text-align: center;
            padding: 10px;
            color: #667eea;
            font-weight: bold;
            font-size: 1.3em;
        }
        
        .vs-text::before { content: "VS"; }
    </style>
</head>
<body>
    <header>
        <a href="../../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="registratie.php">Inschrijving</a></button>
            <button><a href="schema_all.php">Schema</a></button>
            <button><a href="beheer_spelers.php">Deelnemers</a></button>
            <button><a href="admin/login.php">Admin</a></button>
        </nav>
    </header>

    <div class="container">
        <div class="finals-container">
            <h1>👑 King of the Court 👑</h1>
            <p>De Legendarische Finales</p>
        </div>

        <?php if ($groep1_kotc): ?>
            <div class="group-finalists">
                <h2>Groep 1 - Baan 1 Winnaars (Ronde 6)</h2>
                
                <?php
                    $t1s1q = "SELECT naam FROM spelers WHERE id = " . $groep1_kotc['team1_speler1'];
                    $t1s1n = $PDO->query($t1s1q)->fetch()['naam'];
                    
                    $t1s2q = "SELECT naam FROM spelers WHERE id = " . $groep1_kotc['team1_speler2'];
                    $t1s2n = $PDO->query($t1s2q)->fetch()['naam'];
                    
                    $t2s1q = "SELECT naam FROM spelers WHERE id = " . $groep1_kotc['team2_speler1'];
                    $t2s1n = $PDO->query($t2s1q)->fetch()['naam'];
                    
                    $t2s2q = "SELECT naam FROM spelers WHERE id = " . $groep1_kotc['team2_speler2'];
                    $t2s2n = $PDO->query($t2s2q)->fetch()['naam'];
                ?>
                
                <div class="finalist-pair">
                    <strong>🥇 Winna Groep 1:</strong>
                    <p><?= htmlspecialchars($t1s1n) ?> & <?= htmlspecialchars($t1s2n) ?></p>
                </div>
                
                <?php if ($groep1_kotc['winner'] == 2): ?>
                    <div class="finalist-pair">
                        <strong>Runner-up:</strong>
                        <p><?= htmlspecialchars($t2s1n) ?> & <?= htmlspecialchars($t2s2n) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($groep2_kotc): ?>
            <div class="group-finalists">
                <h2>Groep 2 - Baan 1 Winnaars (Ronde 6)</h2>
                
                <?php
                    $t1s1q = "SELECT naam FROM spelers WHERE id = " . $groep2_kotc['team1_speler1'];
                    $t1s1n = $PDO->query($t1s1q)->fetch()['naam'];
                    
                    $t1s2q = "SELECT naam FROM spelers WHERE id = " . $groep2_kotc['team1_speler2'];
                    $t1s2n = $PDO->query($t1s2q)->fetch()['naam'];
                    
                    $t2s1q = "SELECT naam FROM spelers WHERE id = " . $groep2_kotc['team2_speler1'];
                    $t2s1n = $PDO->query($t2s1q)->fetch()['naam'];
                    
                    $t2s2q = "SELECT naam FROM spelers WHERE id = " . $groep2_kotc['team2_speler2'];
                    $t2s2n = $PDO->query($t2s2q)->fetch()['naam'];
                ?>
                
                <div class="finalist-pair">
                    <strong>🥇 Winna Groep 2:</strong>
                    <p><?= htmlspecialchars($t2s1n) ?> & <?= htmlspecialchars($t2s2n) ?></p>
                </div>
                
                <?php if ($groep2_kotc['winner'] == 1): ?>
                    <div class="finalist-pair">
                        <strong>Runner-up:</strong>
                        <p><?= htmlspecialchars($t1s1n) ?> & <?= htmlspecialchars($t1s2n) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($groep1_kotc && $groep2_kotc): ?>
            <div style="background: #fff8e1; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
                <h3 style="color: #ff9800; margin-top: 0;">🏆 ULTIMATE FINALE 🏆</h3>
                <p style="font-size: 1.1em; color: #333;">
                    <?php 
                        // Team 1 van groep 1
                        $g1t1s1q = "SELECT naam FROM spelers WHERE id = " . $groep1_kotc['team1_speler1'];
                        $g1t1s1n = $PDO->query($g1t1s1q)->fetch()['naam'];
                        
                        $g1t1s2q = "SELECT naam FROM spelers WHERE id = " . $groep1_kotc['team1_speler2'];
                        $g1t1s2n = $PDO->query($g1t1s2q)->fetch()['naam'];
                        
                        // Team 1 van groep 2
                        $g2t1s1q = "SELECT naam FROM spelers WHERE id = " . $groep2_kotc['team1_speler1'];
                        $g2t1s1n = $PDO->query($g2t1s1q)->fetch()['naam'];
                        
                        $g2t1s2q = "SELECT naam FROM spelers WHERE id = " . $groep2_kotc['team1_speler2'];
                        $g2t1s2n = $PDO->query($g2t1s2q)->fetch()['naam'];
                    ?>
                    
                    <strong><?= htmlspecialchars($g1t1s1n) ?> & <?= htmlspecialchars($g1t1s2n) ?></strong>
                </p>
                <div class="vs-text"></div>
                <p style="font-size: 1.1em; color: #333;">
                    <strong><?= htmlspecialchars($g2t1s1n) ?> & <?= htmlspecialchars($g2t1s2n) ?></strong>
                </p>
            </div>
        <?php else: ?>
            <p style="color: #999; text-align: center; padding: 40px;">De finales zijn nog niet gereed. Wacht tot ronde 6 is afgerond voor beide groepen.</p>
        <?php endif; ?>

        <hr style="margin: 40px 0;">
        
        <div style="text-align: center;">
            <a href="../../index.php" class="btn-link">← Terug naar Home</a>
            <a href="schema.php" class="btn-link">Bekijk Schema →</a>
        </div>
    </div>
</body>
</html>
