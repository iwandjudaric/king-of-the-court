<?php
require_once '../config/db_connection.php';
require_once '../includes/player_functions.php';
require_once '../includes/admin_functions.php';

$message = '';
$message_type = '';

if (isAdminLoggedIn() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $player_id = $_POST['delete_id'];
    if (deletePlayer($PDO, $player_id)) {
        $message = 'Speler verwijderd.';
        $message_type = 'success';
    } else {
        $message = 'Er is een fout opgetreden bij het verwijderen.';
        $message_type = 'error';
    }
}

$players = getAllPlayers($PDO);
$totalCount = count($players);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deelnemers - King of the Courtz</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        tbody tr {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        tbody tr:hover {
            background-color: #f0f8ff;
        }
        tbody tr td:last-child {
            cursor: default;
        }
        tbody tr td:last-child:hover {
            background-color: inherit;
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
        <div class="admin-wrapper">
            <h1>Deelnemers</h1>
            
            <div class="info-box">
                <p><strong>Totaal:</strong> <?= $totalCount ?> / 50</p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message message-<?= $message_type ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($players)): ?>
                <p class="no-data">Geen deelnemers ingeschreven.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Leeftijd</th>
                                <th>Geslacht</th>
                                <th>Speelniveau</th>
                                <?php if (isAdminLoggedIn()): ?>
                                    <th>Telefoon</th>
                                    <th>Email</th>
                                <?php endif; ?>
                                <th>Status</th>
                                <?php if (isAdminLoggedIn()): ?>
                                    <th>Actie</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $index => $player): ?>
                                <?php 
                                    $isReserve = ($index + 1) > 48;
                                    $status = $isReserve ? 'Reserve' : 'Normaal';
                                ?>
                                <tr onclick="window.location.href='speler_detail.php?id=<?= $player['id'] ?>'">
                                    <td><?= htmlspecialchars($player['naam']) ?></td>
                                    <td><?= htmlspecialchars($player['leeftijd']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($player['geslacht'])) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($player['speelniveau'])) ?></td>
                                    <?php if (isAdminLoggedIn()): ?>
                                        <td><?= htmlspecialchars($player['telefoon'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($player['email'] ?? '-') ?></td>
                                    <?php endif; ?>
                                    <td><?= $status ?></td>
                                    <?php if (isAdminLoggedIn()): ?>
                                        <td onclick="event.stopPropagation();">
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="delete_id" value="<?= $player['id'] ?>">
                                                <button type="submit" class="btn-delete" onclick="return confirm('Weet je zeker dat je deze speler wilt verwijderen?');">Verwijder</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="../index.php">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
