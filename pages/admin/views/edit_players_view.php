<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spelers Bewerken - King of the Courtz</title>
    <link rel="stylesheet" href="../../../public/css/style.css">
</head>
<body>
    <header>
        <a href="../../../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="../../registratie.php">Inschrijving</a></button>
            <button><a href="../../schema.php">Schema</a></button>
            <button><a href="../../beheer_spelers.php">Deelnemers</a></button>
            <button><a href="login.php">Admin</a></button>
        </nav>
    </header>

    <div class="pageShell edit-page">
        <div class="pageCard">
            <h1 class="pageTitle">Spelers Bewerken</h1>
            
            <div class="info-box">
                <p><strong>Ingelogd als: <?= htmlspecialchars($_SESSION['admin_username']) ?></strong></p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message message-success">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($players)): ?>
                <p class="no-data">Geen spelers ingeschreven.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Leeftijd</th>
                                <th>Geslacht</th>
                                <th>Speelniveau</th>
                                <th>Telefoon</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Verwijder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $index => $player): ?>
                                <?php 
                                    $isReserve = ($index + 1) > 48;
                                    $status = $isReserve ? 'Reserve' : 'Normaal';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($player['naam']) ?></td>
                                    <td><?= htmlspecialchars($player['leeftijd']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($player['geslacht'])) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($player['speelniveau'])) ?></td>
                                    <td><?= htmlspecialchars($player['telefoon'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($player['email'] ?? '-') ?></td>
                                    <td>
                                        <span style="padding: 5px 10px; border-radius: 4px; background-color: <?= $isReserve ? '#fff3cd' : '#d4edda' ?>; color: <?= $isReserve ? '#856404' : '#155724' ?>;">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="delete_id" value="<?= $player['id'] ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Weet je zeker dat je deze speler wilt verwijderen?');">Verwijder</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="admin_only.php">Terug naar Admin Panel</a>
            </div>
        </div>
    </div>
    <nav class="mobileBottomNav">
        <a href="../../../index.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Home</span>
        </a>
        <a href="../../registratie.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            <span>Inschrijving</span>
        </a>
        <a href="../../schema.php" class="mobileNavItem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4z"/></svg>
            <span>Schema</span>
        </a>
    </nav></body>
</html>
