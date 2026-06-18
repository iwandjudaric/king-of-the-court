<?php
/**
 * King of the Court - Rotatie Systeem Functies
 * Beheert het rotatie-systeem waarbij spelers per ronde van baan verschuiven
 */

define('NUM_BAANEN', 6);
define('SPELERS_PER_BAAN', 4);
define('SPELERS_PER_GROEP', 24);
define('NUM_RONDES', 6);
define('NUM_GROEPEN', 2);

/**
 * Initiële loting & groepindeling
 * Verdeelt 48 spelers in 2 groepen van 24, zet ze op baanen 1-6
 */
function setupInitialRotation($PDO) {
    try {
        // Haal alle spelers op
        $query = "SELECT id, naam, leeftijd, geslacht, speelniveau FROM spelers ORDER BY RAND() LIMIT 48";
        $stmt = $PDO->query($query);
        $allPlayers = $stmt->fetchAll();
        
        if (count($allPlayers) < 48) {
            return false;
        }
        
        // Verdeel in 2 groepen van 24
        $groep1 = array_slice($allPlayers, 0, 24);
        $groep2 = array_slice($allPlayers, 24, 24);
        
        // Clear bestaande ronde setup
        $PDO->exec("DELETE FROM ronde_setup");
        
        // Zet groep 1 op baanen 1-6 (ronde 1)
        setupGroupRound($PDO, 1, 1, $groep1);
        
        // Zet groep 2 op baanen 1-6 (ronde 1)
        setupGroupRound($PDO, 1, 2, $groep2);
        
        // Update toernooistatus
        $PDO->exec("UPDATE toernooi_status SET loting_klaar = 1, huidige_ronde = 1, huidige_groep = 1");
        
        return true;
    } catch (PDOException $e) {
        error_log("setupInitialRotation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Zet een groep spelers op baanen voor een ronde
 */
function setupGroupRound($PDO, $ronde, $groep, $spelers) {
    try {
        $query = "INSERT INTO ronde_setup (ronde, groep, baan, speler_id, partner_speler_id) VALUES (:ronde, :groep, :baan, :speler_id, :partner_id)";
        $stmt = $PDO->prepare($query);
        
        $baan = 1;
        $idx = 0;
        
        // Verdeel spelers per baan (4 spelers = 2 teams)
        while ($idx < count($spelers) && $baan <= NUM_BAANEN) {
            $speler1 = $spelers[$idx++];
            $speler2 = $spelers[$idx++];
            $speler3 = $spelers[$idx++];
            $speler4 = $spelers[$idx++];
            
            // Team 1: speler1 + speler2
            $stmt->execute([
                ':ronde' => $ronde,
                ':groep' => $groep,
                ':baan' => $baan,
                ':speler_id' => $speler1['id'],
                ':partner_id' => $speler2['id']
            ]);
            
            // Team 2: speler3 + speler4
            $stmt->execute([
                ':ronde' => $ronde,
                ':groep' => $groep,
                ':baan' => $baan,
                ':speler_id' => $speler3['id'],
                ':partner_id' => $speler4['id']
            ]);
            
            $baan++;
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("setupGroupRound error: " . $e->getMessage());
        return false;
    }
}

/**
 * Genereer matches voor huidige ronde & groep op basis van ronde_setup
 */
function generateRoundMatches($PDO, $ronde, $groep) {
    try {
        // Delete huidige ronde matches
        $delQuery = "DELETE FROM ronde_resultaten WHERE ronde = :ronde AND groep = :groep";
        $delStmt = $PDO->prepare($delQuery);
        $delStmt->execute([':ronde' => $ronde, ':groep' => $groep]);
        
        // Haal ronde_setup op (gegroepeerd per baan)
        $query = "SELECT * FROM ronde_setup WHERE ronde = :ronde AND groep = :groep ORDER BY baan";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':ronde' => $ronde, ':groep' => $groep]);
        $positions = $stmt->fetchAll();
        
        // Zet om naar baan-format
        $banen = [];
        foreach ($positions as $pos) {
            $baan = $pos['baan'];
            if (!isset($banen[$baan])) {
                $banen[$baan] = [];
            }
            $banen[$baan][] = $pos;
        }
        
        // Genereer matches per baan
        $insQuery = "INSERT INTO ronde_resultaten (ronde, groep, baan, team1_speler1, team1_speler2, team2_speler1, team2_speler2, status) 
                     VALUES (:ronde, :groep, :baan, :t1s1, :t1s2, :t2s1, :t2s2, 'gepland')";
        $insStmt = $PDO->prepare($insQuery);
        
        foreach ($banen as $baanNum => $banen_spelers) {
            if (count($banen_spelers) === 2) {
                // Team 1 & 2 zijn al gedefinieerd via partner info
                // Rij 0 = Team 1, Rij 1 = Team 2
                $team1_s1 = $banen_spelers[0]['speler_id'];
                $team1_s2 = $banen_spelers[0]['partner_speler_id'];
                $team2_s1 = $banen_spelers[1]['speler_id'];
                $team2_s2 = $banen_spelers[1]['partner_speler_id'];
                
                $insStmt->execute([
                    ':ronde' => $ronde,
                    ':groep' => $groep,
                    ':baan' => $baanNum,
                    ':t1s1' => $team1_s1,
                    ':t1s2' => $team1_s2,
                    ':t2s1' => $team2_s1,
                    ':t2s2' => $team2_s2
                ]);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("generateRoundMatches error: " . $e->getMessage());
        return false;
    }
}

/**
 * Verwerk matchresultaten en rouleer spelers naar volgende ronde
 */
function processRoundResults($PDO, $ronde, $groep) {
    try {
        // Haal alle matches op voor deze ronde/groep
        $query = "SELECT * FROM ronde_resultaten WHERE ronde = :ronde AND groep = :groep AND winner IS NOT NULL";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':ronde' => $ronde, ':groep' => $groep]);
        $matches = $stmt->fetchAll();
        
        // Bepaal baan voor elke speler in volgende ronde
        $nextRonde = $ronde + 1;
        if ($nextRonde > NUM_RONDES) {
            return false; // Toernooi voorbij
        }
        
        // Map: speler_id => next_baan
        $nextBaanMap = [];
        
        foreach ($matches as $match) {
            // Team 1 (winnaars gaan omhoog, verliezers omlaag)
            $team1_s1 = $match['team1_speler1'];
            $team1_s2 = $match['team1_speler2'];
            $team2_s1 = $match['team2_speler1'];
            $team2_s2 = $match['team2_speler2'];
            
            if ($match['winner'] == 1) {
                // Team 1 wint - gaan naar volgende baan
                $nextBaanMap[$team1_s1] = getNextBaan($match['baan'], 1);
                $nextBaanMap[$team1_s2] = getNextBaan($match['baan'], 1);
                
                // Team 2 verliest - gaan naar vorige baan
                $nextBaanMap[$team2_s1] = getNextBaan($match['baan'], -1);
                $nextBaanMap[$team2_s2] = getNextBaan($match['baan'], -1);
            } else {
                // Team 2 wint
                $nextBaanMap[$team2_s1] = getNextBaan($match['baan'], 1);
                $nextBaanMap[$team2_s2] = getNextBaan($match['baan'], 1);
                
                // Team 1 verliest
                $nextBaanMap[$team1_s1] = getNextBaan($match['baan'], -1);
                $nextBaanMap[$team1_s2] = getNextBaan($match['baan'], -1);
            }
        }
        
        // Zet spelers in volgende ronde (shuffle partners per baan)
        rotatePlayersToNextRound($PDO, $nextRonde, $groep, $nextBaanMap);
        
        return true;
    } catch (PDOException $e) {
        error_log("processRoundResults error: " . $e->getMessage());
        return false;
    }
}

/**
 * Bereken volgende baan na winnen/verliezen
 * Winnen: +1 baan (6 wrapt naar 1)
 * Verliezen: -1 baan (1 wrapt naar 6)
 */
function getNextBaan($huidige_baan, $direction) {
    $next = $huidige_baan + $direction;
    if ($next > NUM_BAANEN) {
        return 1;
    } elseif ($next < 1) {
        return NUM_BAANEN;
    }
    return $next;
}

/**
 * Plaats spelers in volgende ronde met nieuwe partners op hun baanen
 */
function rotatePlayersToNextRound($PDO, $ronde, $groep, $nextBaanMap) {
    try {
        // Groepeer spelers per baan
        $banen = [];
        foreach ($nextBaanMap as $speler_id => $baan) {
            if (!isset($banen[$baan])) {
                $banen[$baan] = [];
            }
            $banen[$baan][] = $speler_id;
        }
        
        // Shuffle partners per baan en insert
        $query = "INSERT INTO ronde_setup (ronde, groep, baan, speler_id, partner_speler_id) VALUES (:ronde, :groep, :baan, :speler_id, :partner_id)";
        $stmt = $PDO->prepare($query);
        
        foreach ($banen as $baanNum => $spelers) {
            if (count($spelers) === 4) {
                // Shuffle voor nieuwe partners
                shuffle($spelers);
                
                // Team 1 & 2
                $stmt->execute([
                    ':ronde' => $ronde,
                    ':groep' => $groep,
                    ':baan' => $baanNum,
                    ':speler_id' => $spelers[0],
                    ':partner_id' => $spelers[1]
                ]);
                
                $stmt->execute([
                    ':ronde' => $ronde,
                    ':groep' => $groep,
                    ':baan' => $baanNum,
                    ':speler_id' => $spelers[2],
                    ':partner_id' => $spelers[3]
                ]);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("rotatePlayersToNextRound error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update matchresultaat
 */
function updateRotationMatchResult($PDO, $ronde, $groep, $baan, $score_team1, $score_team2) {
    try {
        $winner = ($score_team1 > $score_team2) ? 1 : 2;
        
        $query = "UPDATE ronde_resultaten 
                 SET score_team1 = :s1, score_team2 = :s2, winner = :winner, status = 'afgerond'
                 WHERE ronde = :ronde AND groep = :groep AND baan = :baan";
        $stmt = $PDO->prepare($query);
        return $stmt->execute([
            ':s1' => $score_team1,
            ':s2' => $score_team2,
            ':winner' => $winner,
            ':ronde' => $ronde,
            ':groep' => $groep,
            ':baan' => $baan
        ]);
    } catch (PDOException $e) {
        error_log("updateRotationMatchResult error: " . $e->getMessage());
        return false;
    }
}

/**
 * Haal King of the Court finalists (baan 1 winnaars na 6 rondes)
 */
function getKingOfCourtCandidates($PDO) {
    try {
        $query = "SELECT * FROM ronde_resultaten 
                 WHERE ronde = 6 AND baan = 1 AND winner IS NOT NULL";
        $stmt = $PDO->query($query);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("getKingOfCourtCandidates error: " . $e->getMessage());
        return [];
    }
}

/**
 * Haal huidige ronde-setup voor groep
 */
function getRondeSetup($PDO, $ronde, $groep) {
    try {
        $query = "SELECT * FROM ronde_setup 
                 WHERE ronde = :ronde AND groep = :groep 
                 ORDER BY baan, speler_id";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':ronde' => $ronde, ':groep' => $groep]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("getRondeSetup error: " . $e->getMessage());
        return [];
    }
}

/**
 * Haal ronde-resultaten (afgeronde matches)
 */
function getRondeResultaten($PDO, $ronde, $groep) {
    try {
        $query = "SELECT * FROM ronde_resultaten 
                 WHERE ronde = :ronde AND groep = :groep 
                 ORDER BY baan";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':ronde' => $ronde, ':groep' => $groep]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("getRondeResultaten error: " . $e->getMessage());
        return [];
    }
}

/**
 * Bereken statistieken per speler
 */
function getPlayerStats($PDO, $speler_id) {
    try {
        // Count total wins (team1 win or team2 win)
        $queryWins = "SELECT COUNT(*) as wins FROM ronde_resultaten 
                    WHERE ((team1_speler1 = ? OR team1_speler2 = ?) AND winner = 1)
                       OR ((team2_speler1 = ? OR team2_speler2 = ?) AND winner = 2)";
        $stmtWins = $PDO->prepare($queryWins);
        $stmtWins->execute([$speler_id, $speler_id, $speler_id, $speler_id]);
        $totalWins = $stmtWins->fetch()['wins'] ?? 0;
        
        // Count total losses (team1 loss or team2 loss)
        $queryLosses = "SELECT COUNT(*) as losses FROM ronde_resultaten 
                      WHERE ((team1_speler1 = ? OR team1_speler2 = ?) AND winner = 2)
                         OR ((team2_speler1 = ? OR team2_speler2 = ?) AND winner = 1)";
        $stmtLosses = $PDO->prepare($queryLosses);
        $stmtLosses->execute([$speler_id, $speler_id, $speler_id, $speler_id]);
        $totalLosses = $stmtLosses->fetch()['losses'] ?? 0;
        
        return [
            'wins' => $totalWins,
            'losses' => $totalLosses,
            'winrate' => ($totalWins + $totalLosses > 0) ? ($totalWins / ($totalWins + $totalLosses)) * 100 : 0
        ];
    } catch (PDOException $e) {
        error_log("getPlayerStats error: " . $e->getMessage());
        return ['wins' => 0, 'losses' => 0, 'winrate' => 0];
    }
}
?>
