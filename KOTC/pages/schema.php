<?php
require_once '../config/db_connection.php';
require_once '../includes/admin_functions.php';

// LOGICA
$matches = getAllMatches($PDO);
$updateMessage = '';

if (isAdminLoggedIn() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $matchId = $_POST['match_id'];
    $team1Score = $_POST['team1_score'] ?? 0;
    $team2Score = $_POST['team2_score'] ?? 0;
    
    if (updateMatchResult($PDO, $matchId, $team1Score, $team2Score)) {
        $updateMessage = 'Uitslag bijgewerkt!';
        $matches = getAllMatches($PDO);
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schema - King of the Courtz</title>
    <link rel="stylesheet" href="../public/css/style.css">
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
        <div class="admin-wrapper">
            <h1>Wedstrijdschema</h1>
            
            <?php if (!empty($updateMessage)): ?>
                <div class="message message-success">
                    <p><?= htmlspecialchars($updateMessage) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($matches)): ?>
                <p class="no-data">Geen wedstrijden gepland. Wacht tot de loting is gedaan.</p>
            <?php else: ?>
                <?php
                    $groups = [];
                    foreach ($matches as $match) {
                        $ronde = $match['ronde'];
                        if (!isset($groups[$ronde])) {
                            $groups[$ronde] = [];
                        }
                        $groups[$ronde][] = $match;
                    }
                ?>
                
                <?php foreach ($groups as $ronde => $roundMatches): ?>
                    <div>
                        <h2>Ronde <?= $ronde ?></h2>
                        
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Baan</th>
                                        <th>Team 1</th>
                                        <th>Team 2</th>
                                        <th>Uitslag</th>
                                        <?php if (isAdminLoggedIn()): ?>
                                            <th>Wijzig</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roundMatches as $match): ?>
                                        <tr>
                                            <td><?= $match['baan'] ?></td>
                                            <td>
                                                <?php
                                                    $t1s1 = $match['team1_speler1_naam'] ?? 'Onbekend';
                                                    $t1s2 = $match['team1_speler2_naam'] ?? 'Onbekend';
                                                ?>
                                                <?= htmlspecialchars($t1s1) ?> / <?= htmlspecialchars($t1s2) ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $t2s1 = $match['team2_speler1_naam'] ?? 'Onbekend';
                                                    $t2s2 = $match['team2_speler2_naam'] ?? 'Onbekend';
                                                ?>
                                                <?= htmlspecialchars($t2s1) ?> / <?= htmlspecialchars($t2s2) ?>
                                            </td>
                                            <td>
                                                <?php if ($match['status'] === 'afgerond'): ?>
                                                    <?= $match['team1_score'] ?> - <?= $match['team2_score'] ?>
                                                <?php else: ?>
                                                    <em>Niet gespeeld</em>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (isAdminLoggedIn()): ?>
                                                <td>
                                                    <?php if ($match['status'] !== 'afgerond'): ?>
                                                        <form method="POST" action="" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                                                            <input type="number" name="team1_score" min="0" placeholder="T1" required style="width: 50px;">
                                                            <input type="number" name="team2_score" min="0" placeholder="T2" required style="width: 50px;">
                                                            <button type="submit" class="btn-delete">Opslaan</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span style="color: #666;">Afgerond</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="../index.php">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
