<?php
require_once 'config/db_connection.php';
require_once 'includes/admin_functions.php';

// Test with player 1 (we know they should have matches)
$playerId = 1;

// Direct query test
echo "<h2>Testing getPlayerMatches() for Player $playerId</h2>";

$matches = getPlayerMatches($PDO, $playerId);

echo "<pre>";
echo "Matches found: " . count($matches) . "\n";
echo "Data:\n";
var_dump($matches);
echo "</pre>";

// Also test raw query
echo "<h2>Raw Query Test</h2>";
$query = "SELECT m.*, 
          s1.naam as team1_speler1_naam, s2.naam as team1_speler2_naam,
          s3.naam as team2_speler1_naam, s4.naam as team2_speler2_naam
          FROM matches m
          LEFT JOIN spelers s1 ON m.team1_speler1 = s1.id
          LEFT JOIN spelers s2 ON m.team1_speler2 = s2.id
          LEFT JOIN spelers s3 ON m.team2_speler1 = s3.id
          LEFT JOIN spelers s4 ON m.team2_speler2 = s4.id
          WHERE m.team1_speler1 = :pid OR m.team1_speler2 = :pid 
             OR m.team2_speler1 = :pid OR m.team2_speler2 = :pid
          ORDER BY m.ronde, m.baan";

$stmt = $PDO->prepare($query);
$stmt->execute([':pid' => $playerId]);
$rawMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
echo "Raw Matches found: " . count($rawMatches) . "\n";
echo "Data:\n";
var_dump($rawMatches);
echo "</pre>";

// Test if matches exist at all
echo "<h2>Total Matches in Database</h2>";
$totalQuery = "SELECT COUNT(*) as count FROM matches";
$stmt = $PDO->prepare($totalQuery);
$stmt->execute();
$total = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total matches: " . $total['count'] . "\n";

// Test if player 1 exists
echo "<h2>Player 1 Exists?</h2>";
$playerQuery = "SELECT * FROM spelers WHERE id = 1";
$stmt = $PDO->prepare($playerQuery);
$stmt->execute();
$player = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($player);
echo "</pre>";

// List first 3 matches
echo "<h2>First 3 Matches in Database</h2>";
$firstQuery = "SELECT m.*, s1.naam as t1_s1, s2.naam as t1_s2, s3.naam as t2_s1, s4.naam as t2_s2
              FROM matches m
              LEFT JOIN spelers s1 ON m.team1_speler1 = s1.id
              LEFT JOIN spelers s2 ON m.team1_speler2 = s2.id
              LEFT JOIN spelers s3 ON m.team2_speler1 = s3.id
              LEFT JOIN spelers s4 ON m.team2_speler2 = s4.id
              LIMIT 3";
$stmt = $PDO->prepare($firstQuery);
$stmt->execute();
$firstMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($firstMatches);
echo "</pre>";
?>
