<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/player_functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$message = '';
$players = getAllPlayers($PDO);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $player_id = $_POST['delete_id'];
    if (deletePlayer($PDO, $player_id)) {
        $message = 'Speler verwijderd.';
        $players = getAllPlayers($PDO);
    } else {
        $message = 'Er is een fout opgetreden bij het verwijderen.';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spelers Bewerken - King of the Courtz</title>
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
            <h1>Spelers Bewerken</h1>
            
            <div class="info-box">
                <p><strong>Ingelogd als: <?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message message-success">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($players)): ?>
                <p class="no-data">Geen spelers ingeschreven.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Leeftijd</th>
                                <th>Geslacht</th>
                                <th>Speelniveau</th>
                                <th>Telefoon</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Verwijder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $index => $player): ?>
                                <?php 
                                    $isReserve = ($index + 1) > 48;
                                    $status = $isReserve ? 'Reserve' : 'Normaal';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($player['naam']) ?></td>
                                    <td><?= htmlspecialchars($player['leeftijd']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($player['geslacht'])) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($player['speelniveau'])) ?></td>
                                    <td><?= htmlspecialchars($player['telefoon'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($player['email'] ?? '-') ?></td>
                                    <td>
                                        <span style="padding: 5px 10px; border-radius: 4px; background-color: <?= $isReserve ? '#fff3cd' : '#d4edda' ?>; color: <?= $isReserve ? '#856404' : '#155724' ?>;">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="delete_id" value="<?= $player['id'] ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Weet je zeker dat je deze speler wilt verwijderen?');">Verwijder</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="admin_only.php">Terug naar Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
