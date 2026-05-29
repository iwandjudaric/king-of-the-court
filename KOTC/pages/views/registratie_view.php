<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie - King of the Courtz</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <header>
        <a href="../../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="registratie.php">Inschrijving</a></button>
            <button><a href="schema_all.php">Schema</a></button>
            <button><a href="beheer_spelers.php">Deelnemers</a></button>
            <button><a href="admin/login.php">Admin</a></button>
        </nav>
    </header>

    <div class="pageShell registratie-page">
        <div class="pageCard">
            <h1 class="pageTitle">Speler Inschrijving</h1>
            
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
                <a href="../../index.php">← Terug naar home</a>
            </div>
        </div>
    </div>

    <nav class="mobileBottomNav">
        <a href="../../index.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Home</span>
        </a>
        <a href="registratie.php" class="mobileNavItem active">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            <span>Inschrijving</span>
        </a>
        <a href="schema.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4z"/></svg>
            <span>Schema</span>
        </a>
    </nav>
</body>
</html>
