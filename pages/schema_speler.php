<?php
require_once '../config/db_connection.php';
require_once '../includes/admin_functions.php';

// LOGICA
$playerId = $_GET['player_id'] ?? null;
$playerName = '';
$playerMatches = [];

if ($playerId) {
    // Haal speler info
    $query = "SELECT id, naam FROM spelers WHERE id = :id";
    $stmt = $PDO->prepare($query);
    $stmt->execute([':id' => $playerId]);
    $player = $stmt->fetch();
    
    if ($player) {
        $playerName = $player['naam'];
        $playerMatches = getPlayerMatches($PDO, $playerId);
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Schema - King of the Courtz</title>
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
        <?php
        if (!$playerId || !$player) {
            echo '<div class="pageShell"><p>Selecteer een speler om het schema te zien.</p></div>';
        } else {
            include '../pages/views/schema_speler_view.php';
        }
        ?>
    </div>

    <nav class="mobileBottomNav">
        <a href="../index.php">Home</a>
        <a href="schema_all.php">Schema</a>
        <a href="beheer_spelers.php">Spelers</a>
    </nav>
</body>
</html>
