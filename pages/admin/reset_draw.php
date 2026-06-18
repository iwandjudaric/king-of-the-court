<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete all matches
        $query = "DELETE FROM matches";
        $stmt = $PDO->prepare($query);
        $stmt->execute();
        
        // Reset loting_klaar flag
        $query = "UPDATE toernooi_status SET loting_klaar = 0";
        $stmt = $PDO->prepare($query);
        $stmt->execute();
        
        $success = true;
        $message = 'Alle wedstrijden verwijderd! Je kunt nu opnieuw loten.';
    } catch (PDOException $e) {
        $success = false;
        $message = 'Fout: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Loting - Admin</title>
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
            <h1>Reset Loting</h1>
            
            <?php if (isset($message)): ?>
                <div class="message message-<?= $success ? 'success' : 'error' ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!isset($message)): ?>
                <div class="info-box">
                    <p><strong>⚠️ Let op!</strong> Dit zal ALLE wedstrijden verwijderen!</p>
                    <p>Je kunt dan opnieuw op "Loting Uitvoeren" klikken met de verbeterde code.</p>
                    
                    <form method="POST" action="" style="margin-top: 20px;">
                        <button type="submit" class="btn-delete" onclick="return confirm('Weet je zeker? Dit verwijdert ALLE wedstrijden!')">
                            ✓ Ja, verwijder alle wedstrijden
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="back-link" style="margin-top: 20px;">
                    <a href="dashboard.php">→ Terug naar Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
