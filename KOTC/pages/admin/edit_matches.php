<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$message = '';
$matches = getAllMatches($PDO);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $matchId = $_POST['match_id'];
    $team1Score = $_POST['team1_score'] ?? 0;
    $team2Score = $_POST['team2_score'] ?? 0;
    
    if (updateMatchResult($PDO, $matchId, $team1Score, $team2Score)) {
        $message = 'Uitslag bijgewerkt!';
        $matches = getAllMatches($PDO);
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedstrijden Bewerken - King of the Courtz</title>
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
            <h1>Wedstrijden Bewerken</h1>
            
            <div class="info-box">
                <p><strong>Ingelogd als: <?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message message-success">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($matches)): ?>
                <p class="no-data">Geen wedstrijden beschikbaar. Voer eerst de loting uit.</p>
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
                    <div style="margin-bottom: 30px;">
                        <h2>Ronde <?= $ronde ?></h2>
                        
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Baan</th>
                                        <th>Team 1</th>
                                        <th>Team 2</th>
                                        <th>Uitslag</th>
                                        <th>Status</th>
                                        <th>Wijzig</th>
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
                                                    <strong><?= $match['team1_score'] ?> - <?= $match['team2_score'] ?></strong>
                                                <?php else: ?>
                                                    <em>Niet gespeeld</em>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span style="padding: 5px 10px; border-radius: 4px; background-color: <?= $match['status'] === 'afgerond' ? '#d4edda' : '#fff3cd' ?>; color: <?= $match['status'] === 'afgerond' ? '#155724' : '#856404' ?>;">
                                                    <?= ucfirst($match['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                                                    <input type="number" name="team1_score" min="0" placeholder="T1" value="<?= $match['team1_score'] ?? '' ?>" required style="width: 50px;">
                                                    <input type="number" name="team2_score" min="0" placeholder="T2" value="<?= $match['team2_score'] ?? '' ?>" required style="width: 50px;">
                                                    <button type="submit" name="action" value="update_match" class="btn-delete">Opslaan</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="admin_only.php">Terug naar Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
