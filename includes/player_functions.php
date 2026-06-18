<?php

function validatePlayerData($data) {
    $errors = [];
    
    if (empty($data['naam'])) {
        $errors['naam'] = 'Naam is verplicht';
    } elseif (strlen($data['naam']) < 2) {
        $errors['naam'] = 'Naam moet minstens 2 karakters zijn';
    } elseif (!preg_match("/^[a-zA-Z\s]*$/", $data['naam'])) {
        $errors['naam'] = 'Naam mag alleen letters en spaties bevatten';
    }
    
    if (empty($data['leeftijd'])) {
        $errors['leeftijd'] = 'Leeftijd is verplicht';
    } elseif (!is_numeric($data['leeftijd']) || $data['leeftijd'] < 1 || $data['leeftijd'] > 120) {
        $errors['leeftijd'] = 'Leeftijd moet tussen 1 en 120 zijn';
    }
    
    if (empty($data['geslacht'])) {
        $errors['geslacht'] = 'Geslacht is verplicht';
    } elseif (!in_array($data['geslacht'], ['man', 'vrouw', 'anders'])) {
        $errors['geslacht'] = 'Ongeldige keuze voor geslacht';
    }
    
    if (empty($data['speelniveau'])) {
        $errors['speelniveau'] = 'Speelniveau is verplicht';
    } elseif (!in_array($data['speelniveau'], ['beginner', 'intermediate', 'gevorderd'])) {
        $errors['speelniveau'] = 'Ongeldige keuze voor speelniveau';
    }
    
    if (!empty($data['telefoon'])) {
        if (!preg_match("/^[0-9\s\-\+]*$/", $data['telefoon'])) {
            $errors['telefoon'] = 'Telefoonnummer bevat ongeldige karakters';
        } elseif (strlen(preg_replace("/[^0-9]/", "", $data['telefoon'])) < 10) {
            $errors['telefoon'] = 'Telefoonnummer moet minstens 10 cijfers bevatten';
        }
    }
    
    if (!empty($data['email'])) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is ongeldig';
        }
    }
    
    if (empty($data['telefoon']) && empty($data['email'])) {
        $errors['contact'] = 'Voer minstens een telefoonnummer of email in';
    }
    
    return $errors;
}

function addPlayer($PDO, $data) {
    try {
        $count = getPlayerCount($PDO);
        
        if ($count >= 50) {
            return false;
        }
        
        $query = "INSERT INTO spelers (naam, leeftijd, geslacht, speelniveau, telefoon, email) 
                  VALUES (:naam, :leeftijd, :geslacht, :speelniveau, :telefoon, :email)";
        
        $stmt = $PDO->prepare($query);
        $stmt->execute([
            ':naam' => $data['naam'],
            ':leeftijd' => $data['leeftijd'],
            ':geslacht' => $data['geslacht'],
            ':speelniveau' => $data['speelniveau'],
            ':telefoon' => !empty($data['telefoon']) ? $data['telefoon'] : null,
            ':email' => !empty($data['email']) ? $data['email'] : null
        ]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getPlayerCount($PDO) {
    try {
        $query = "SELECT COUNT(*) as count FROM spelers";
        $stmt = $PDO->query($query);
        $result = $stmt->fetch();
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}

function getAllPlayers($PDO) {
    try {
        $query = "SELECT * FROM spelers ORDER BY naam ASC";
        $stmt = $PDO->query($query);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function deletePlayer($PDO, $player_id) {
    try {
        $query = "DELETE FROM spelers WHERE id = :id";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':id' => $player_id]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

?>
