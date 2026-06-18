<?php
require_once '../config/db_connection.php';
require_once '../includes/rotation_functions.php';

// Haal alle spelers op
$query = "SELECT id, naam FROM spelers ORDER BY naam ASC";
$stmt = $PDO->prepare($query);
$stmt->execute();
$players = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spelers Schema - King of the Courtz</title>
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

    <div class="pageShell">
        <div class="pageCard">
            <h1 class="pageTitle">Kies een speler om het schema te bekijken</h1>
            
            <?php if (empty($players)): ?>
                <p>Geen spelers gevonden.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <style>
                        .clickable-row:hover {
                            background-color: #f5f5f5;
                        }
                        .clickable-row td a {
                            display: block;
                            width: 100%;
                            height: 100%;
                            text-decoration: none;
                            color: #333;
                            padding: 15px;
                        }
                        .clickable-row td {
                            padding: 0; /* Remove default padding so the link fills the cell completely */
                        }
                    </style>
                    <table class="data-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 15px;">Speler Naam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player): ?>
                                <tr class="clickable-row" style="cursor: pointer; transition: background 0.2s;">
                                    <td>
                                        <a href="schema_speler_rotation.php?speler_id=<?= $player['id'] ?>" style="font-weight: bold;">
                                            <?= htmlspecialchars($player['naam']) ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="back-link" style="margin-top: 20px;">
                <a href="../index.php">← Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
