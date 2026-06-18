<?php
require_once 'config/db_connection.php';

// Check welke speler IDs in matches voorkomen
$query = "SELECT DISTINCT team1_speler1 as player_id FROM matches 
          UNION 
          SELECT DISTINCT team1_speler2 FROM matches
          UNION 
          SELECT DISTINCT team2_speler1 FROM matches
          UNION 
          SELECT DISTINCT team2_speler2 FROM matches
          ORDER BY player_id";

$stmt = $PDO->prepare($query);
$stmt->execute();
$players_in_matches = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Spelers in matches database: " . implode(", ", $players_in_matches) . "\n\n";

// Now test one specific player
$testPlayerId = 5;
$query2 = "SELECT m.id, m.ronde, m.baan, m.team1_speler1, m.team1_speler2, m.team2_speler1, m.team2_speler2 FROM matches m 
           WHERE m.team1_speler1 = :pid OR m.team1_speler2 = :pid OR m.team2_speler1 = :pid OR m.team2_speler2 = :pid";
$stmt2 = $PDO->prepare($query2);
$stmt2->execute([':pid' => $testPlayerId]);
$matches = $stmt2->fetchAll();

echo "Matches for player $testPlayerId: " . count($matches) . "\n";
foreach ($matches as $match) {
    echo "- Match ID: {$match['id']}, Ronde: {$match['ronde']}, Baan: {$match['baan']}\n";
}
?>
