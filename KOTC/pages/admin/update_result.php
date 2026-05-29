<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/rotation_functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ronde = intval($_POST['ronde'] ?? 0);
    $groep = intval($_POST['groep'] ?? 0);
    $baan = intval($_POST['baan'] ?? 0);
    $score_team1 = intval($_POST['score_team1'] ?? 0);
    $score_team2 = intval($_POST['score_team2'] ?? 0);
    
    if ($ronde > 0 && $groep > 0 && $baan > 0) {
        if (updateRotationMatchResult($PDO, $ronde, $groep, $baan, $score_team1, $score_team2)) {
            header("Location: dashboard_rotation.php?success=1");
            exit();
        } else {
            header("Location: dashboard_rotation.php?error=1");
            exit();
        }
    }
}

header("Location: dashboard_rotation.php");
exit();
?>
