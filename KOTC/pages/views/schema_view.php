<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schema - King of the Courtz</title>
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

    <div class="pageShell schema-page schedulePage">
        <div class="pageCard">
            <h1 class="pageTitle">Wedstrijdschema</h1>
            
            <?php if (!empty($updateMessage)): ?>
                <div class="message message-success">
                    <p><?= htmlspecialchars($updateMessage) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($matches)): ?>
                <p class="no-data">Geen wedstrijden gepland. Wacht tot de loting is gedaan.</p>
            <?php else: ?>
                <?php
                    $groups = [];
                    foreach ($matches as $match) {
                        $ronde = $match['ronde'];
                        if (!isset($groups[$ronde])) {
                            $groups[$ronde] = [];
                        }
                        $groups[$ronde][] = $match;
                    }
                ?>
                
                <?php foreach ($groups as $ronde => $roundMatches): ?>
                    <div>
                        <h2>Ronde <?= $ronde ?></h2>
                        
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Baan</th>
                                        <th>Team 1</th>
                                        <th>Team 2</th>
                                        <th>Uitslag</th>
                                        <?php if (isAdminLoggedIn()): ?>
                                            <th>Wijzig</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roundMatches as $match): ?>
                                        <tr>
                                            <td><?= $match['baan'] ?></td>
                                            <td>
                                                <?php
                                                    $t1s1 = $match['team1_speler1_naam'] ?? 'Onbekend';
                                                    $t1s2 = $match['team1_speler2_naam'] ?? 'Onbekend';
                                                ?>
                                                <?= htmlspecialchars($t1s1) ?> / <?= htmlspecialchars($t1s2) ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $t2s1 = $match['team2_speler1_naam'] ?? 'Onbekend';
                                                    $t2s2 = $match['team2_speler2_naam'] ?? 'Onbekend';
                                                ?>
                                                <?= htmlspecialchars($t2s1) ?> / <?= htmlspecialchars($t2s2) ?>
                                            </td>
                                            <td>
                                                <?php if ($match['status'] === 'afgerond'): ?>
                                                    <?= $match['team1_score'] ?> - <?= $match['team2_score'] ?>
                                                <?php else: ?>
                                                    <em>Niet gespeeld</em>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (isAdminLoggedIn()): ?>
                                                <td>
                                                    <?php if ($match['status'] !== 'afgerond'): ?>
                                                        <form method="POST" action="" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                                                            <input type="number" name="team1_score" min="0" placeholder="T1" required style="width: 50px;">
                                                            <input type="number" name="team2_score" min="0" placeholder="T2" required style="width: 50px;">
                                                            <button type="submit" class="btn-delete">Opslaan</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span style="color: #666;">Afgerond</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
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
        <a href="registratie.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            <span>Inschrijving</span>
        </a>
        <a href="schema.php" class="mobileNavItem active">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4z"/></svg>
            <span>Schema</span>
        </a>
    </nav>
</body>
</html>
