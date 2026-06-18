<?php
require_once '../config/db_connection.php';
require_once '../includes/player_functions.php';
require_once '../includes/admin_functions.php';

// LOGICA
$errors = [];
$success = false;
$reserveWarning = false;
$formData = [
    'naam' => '',
    'leeftijd' => '',
    'geslacht' => '',
    'speelniveau' => '',
    'telefoon' => '',
    'email' => ''
];

$playerCount = getPlayerCount($PDO);
$isClosed = isTournamentClosed($PDO);
$isReserve = $playerCount >= 48;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isClosed) {
    $formData = [
        'naam' => $_POST['naam'] ?? '',
        'leeftijd' => $_POST['leeftijd'] ?? '',
        'geslacht' => $_POST['geslacht'] ?? '',
        'speelniveau' => $_POST['speelniveau'] ?? '',
        'telefoon' => $_POST['telefoon'] ?? '',
        'email' => $_POST['email'] ?? ''
    ];
    
    $errors = validatePlayerData($formData);
    
    if (empty($errors)) {
        if (addPlayer($PDO, $formData)) {
            $success = true;
            $playerCount = getPlayerCount($PDO);
            
            if ($playerCount >= 49) {
                $reserveWarning = true;
            }
            
            $formData = [
                'naam' => '',
                'leeftijd' => '',
                'geslacht' => '',
                'speelniveau' => '',
                'telefoon' => '',
                'email' => ''
            ];
        } else {
            $errors['database'] = 'Er is een fout opgetreden. Probeer het later opnieuw.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie - King of the Courtz</title>
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
        <div class="form-wrapper">
            <h1>Speler Inschrijving</h1>
            
            <div class="info-box">
                <p><strong>Ingeschreven:</strong> <?= $playerCount ?> / 50</p>
            </div>
            
            <?php if ($isClosed): ?>
                <div class="message message-error">
                    <p>Inschrijving is gesloten. Maximum aantal deelnemers bereikt.</p>
                </div>
            <?php else: ?>
                <?php if ($success): ?>
                    <div class="message message-success">
                        <p>Bedankt voor je inschrijving!</p>
                        <?php if ($reserveWarning): ?>
                            <p>Let op: Je bent ingeschreven als reservespeler.</p>
                        <?php else: ?>
                            <p>Je bent succesvol ingeschreven.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors['database'])): ?>
                    <div class="message message-error">
                        <p><?= htmlspecialchars($errors['database']) ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" novalidate>
                    <div class="form-group">
                        <label for="naam">Naam (minimaal 2 karakters)</label>
                        <input 
                            type="text" 
                            id="naam" 
                            name="naam" 
                            required
                            placeholder="Minimaal 2 karakters"
                            value="<?= htmlspecialchars($formData['naam']) ?>"
                        >
                        <?php if (!empty($errors['naam'])): ?>
                            <p class="error-message"><?= htmlspecialchars($errors['naam']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="leeftijd">Leeftijd</label>
                        <input 
                            type="number" 
                            id="leeftijd" 
                            name="leeftijd" 
                            required
                            min="1"
                            max="120"
                            value="<?= htmlspecialchars($formData['leeftijd']) ?>"
                        >
                        <?php if (!empty($errors['leeftijd'])): ?>
                            <p class="error-message"><?= htmlspecialchars($errors['leeftijd']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="geslacht">Geslacht</label>
                        <select id="geslacht" name="geslacht" required>
                            <option value="">-- Kies --</option>
                            <option value="man" <?php echo $formData['geslacht'] === 'man' ? 'selected' : '' ?>>Man</option>
                            <option value="vrouw" <?php echo $formData['geslacht'] === 'vrouw' ? 'selected' : '' ?>>Vrouw</option>
                            <option value="anders" <?php echo $formData['geslacht'] === 'anders' ? 'selected' : '' ?>>Anders</option>
                        </select>
                        <?php if (!empty($errors['geslacht'])): ?>
                            <p class="error-message"><?= htmlspecialchars($errors['geslacht']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="speelniveau">Speelniveau</label>
                        <select id="speelniveau" name="speelniveau" required>
                            <option value="">-- Kies --</option>
                            <option value="beginner" <?php echo $formData['speelniveau'] === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="intermediate" <?php echo $formData['speelniveau'] === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="gevorderd" <?php echo $formData['speelniveau'] === 'gevorderd' ? 'selected' : '' ?>>Gevorderd</option>
                        </select>
                        <?php if (!empty($errors['speelniveau'])): ?>
                            <p class="error-message"><?= htmlspecialchars($errors['speelniveau']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefoon">Telefoonnummer (of Email - minstens 1 verplicht)</label>
                        <input 
                            type="tel" 
                            id="telefoon" 
                            name="telefoon"
                            placeholder="Of vul Email in - minstens 1 verplicht"
                            value="<?= htmlspecialchars($formData['telefoon']) ?>"
                        >
                        <?php if (!empty($errors['telefoon'])): ?>
                            <p class="error-message"><?= htmlspecialchars($errors['telefoon']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email (of Telefoonnummer - minstens 1 verplicht)</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="Of vul Telefoonnummer in - minstens 1 verplicht"
                            value="<?= htmlspecialchars($formData['email']) ?>"
                        >
                        <?php if (!empty($errors['email'])): ?>
                            <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($errors['contact'])): ?>
                        <div class="message message-error">
                            <p><?= htmlspecialchars($errors['contact']) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn-submit">Inschrijven</button>
                </form>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="../index.php">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
