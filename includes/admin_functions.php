<?php

define('MAX_PLAYERS', 50);
define('MAX_NORMAL_PLAYERS', 48);
define('TOURNAMENT_START_TIME', 9); // 09:00
define('MINUTES_PER_RONDE', 20); // 15 min match + 5 min buffer

function getRondeTime($ronde) {
    $startHour = TOURNAMENT_START_TIME;
    $minutesPerRonde = MINUTES_PER_RONDE;
    $totalMinutes = ($ronde - 1) * $minutesPerRonde;
    $hours = $startHour + floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    return sprintf("%02d:%02d", $hours, $minutes);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function loginAdmin($PDO, $username, $password) {
    try {
        $query = "SELECT * FROM admins WHERE username = :username";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

function logoutAdmin() {
    session_destroy();
}

function getTournamentStatus($PDO) {
    try {
        $query = "SELECT * FROM toernooi_status LIMIT 1";
        $stmt = $PDO->query($query);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function isTournamentClosed($PDO) {
    $count = getPlayerCount($PDO);
    return $count >= MAX_PLAYERS;
}

function performDraw($PDO) {
    try {
        $query = "SELECT id, naam, leeftijd, geslacht, speelniveau FROM spelers ORDER BY RAND()";
        $stmt = $PDO->query($query);
        $players = $stmt->fetchAll();
        
        if (count($players) < 48) {
            return false;
        }
        
        $normalPlayers = array_slice($players, 0, MAX_NORMAL_PLAYERS);
        $reserves = array_slice($players, MAX_NORMAL_PLAYERS);
        
        $lowSkill = [];
        $mediumSkill = [];
        $highSkill = [];
        
        foreach ($normalPlayers as $player) {
            if ($player['speelniveau'] === 'beginner') {
                $lowSkill[] = $player;
            } elseif ($player['speelniveau'] === 'intermediate') {
                $mediumSkill[] = $player;
            } else {
                $highSkill[] = $player;
            }
        }
        
        $shuffledLow = $lowSkill;
        shuffle($shuffledLow);
        
        $shuffledMedium = $mediumSkill;
        shuffle($shuffledMedium);
        
        $shuffledHigh = $highSkill;
        shuffle($shuffledHigh);
        
        $matches = [];
        $baan = 1;
        $ronde = 1;
        
        $idx_low = 0;
        $idx_med = 0;
        $idx_high = 0;
        
        // Combine all players into one pool for matching
        $allPlayers = array_merge($shuffledLow, $shuffledMedium, $shuffledHigh);
        $totalPlayers = count($allPlayers);
        $currentIdx = 0;
        
        while ($currentIdx + 3 < $totalPlayers) {
            if ($baan > 6) {
                $baan = 1;
                $ronde++;
            }
            
            // Take next 4 players and split into 2 teams
            $p1 = $allPlayers[$currentIdx++];
            $p2 = $allPlayers[$currentIdx++];
            $p3 = $allPlayers[$currentIdx++];
            $p4 = $allPlayers[$currentIdx++];
            
            $team1 = [$p1, $p2];
            $team2 = [$p3, $p4];
            
            if (count($team1) === 2 && count($team2) === 2) {
                $matchQuery = "INSERT INTO matches (baan, ronde, team1_speler1, team1_speler2, team2_speler1, team2_speler2, status) 
                              VALUES (:baan, :ronde, :t1s1, :t1s2, :t2s1, :t2s2, 'gepland')";
                $matchStmt = $PDO->prepare($matchQuery);
                $matchStmt->execute([
                    ':baan' => $baan,
                    ':ronde' => $ronde,
                    ':t1s1' => $team1[0]['id'],
                    ':t1s2' => $team1[1]['id'],
                    ':t2s1' => $team2[0]['id'],
                    ':t2s2' => $team2[1]['id']
                ]);
                
                $matches[] = [
                    'baan' => $baan,
                    'ronde' => $ronde,
                    'team1' => $team1,
                    'team2' => $team2
                ];
                
                $baan++;
            }
        }
        
        $statusQuery = "UPDATE toernooi_status SET loting_klaar = 1";
        $PDO->exec($statusQuery);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getAllMatches($PDO) {
    try {
        $query = "SELECT m.*, 
                  s1.naam as team1_speler1_naam, s2.naam as team1_speler2_naam,
                  s3.naam as team2_speler1_naam, s4.naam as team2_speler2_naam
                  FROM matches m
                  LEFT JOIN spelers s1 ON m.team1_speler1 = s1.id
                  LEFT JOIN spelers s2 ON m.team1_speler2 = s2.id
                  LEFT JOIN spelers s3 ON m.team2_speler1 = s3.id
                  LEFT JOIN spelers s4 ON m.team2_speler2 = s4.id
                  ORDER BY m.ronde, m.baan";
        $stmt = $PDO->query($query);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getMatchDetails($PDO, $matchId) {
    try {
        $query = "SELECT m.*, 
                  s1.naam as team1_speler1_naam, s2.naam as team1_speler2_naam,
                  s3.naam as team2_speler1_naam, s4.naam as team2_speler2_naam
                  FROM matches m
                  LEFT JOIN spelers s1 ON m.team1_speler1 = s1.id
                  LEFT JOIN spelers s2 ON m.team1_speler2 = s2.id
                  LEFT JOIN spelers s3 ON m.team2_speler1 = s3.id
                  LEFT JOIN spelers s4 ON m.team2_speler2 = s4.id
                  WHERE m.id = :id";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':id' => $matchId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function updateMatchResult($PDO, $matchId, $team1_score, $team2_score) {
    try {
        if ($team1_score > $team2_score) {
            $winner = 1;
        } elseif ($team2_score > $team1_score) {
            $winner = 2;
        } else {
            $winner = 0;
        }
        
        $query = "UPDATE matches SET team1_score = :t1_score, team2_score = :t2_score, winner = :winner, status = 'afgerond' WHERE id = :id";
        $stmt = $PDO->prepare($query);
        $stmt->execute([
            ':t1_score' => $team1_score,
            ':t2_score' => $team2_score,
            ':winner' => $winner,
            ':id' => $matchId
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getPlayerMatches($PDO, $playerId) {
    try {
        $query = "SELECT m.*, 
                  s1.naam as team1_speler1_naam, s2.naam as team1_speler2_naam,
                  s3.naam as team2_speler1_naam, s4.naam as team2_speler2_naam
                  FROM matches m
                  LEFT JOIN spelers s1 ON m.team1_speler1 = s1.id
                  LEFT JOIN spelers s2 ON m.team1_speler2 = s2.id
                  LEFT JOIN spelers s3 ON m.team2_speler1 = s3.id
                  LEFT JOIN spelers s4 ON m.team2_speler2 = s4.id
                  WHERE m.team1_speler1 = :pid1 OR m.team1_speler2 = :pid2 OR m.team2_speler1 = :pid3 OR m.team2_speler2 = :pid4
                  ORDER BY m.ronde, m.baan";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':pid1' => $playerId, ':pid2' => $playerId, ':pid3' => $playerId, ':pid4' => $playerId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
?>
