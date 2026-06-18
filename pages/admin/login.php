<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/admin_functions.php';

$error = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    try {
        // Case-insensitive username search
        $query = "SELECT * FROM admins WHERE LOWER(username) = LOWER(:username)";
        $stmt = $PDO->prepare($query);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Gebruikersnaam of wachtwoord incorrect';
        }
    } catch (Exception $e) {
        $error = 'Login fout';
    }
}

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - King of the Courtz</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <header>
        <a href="../../index.php"><h1 class="headerText">King of the Courtz</h1></a>
        <nav>
            <button><a href="../registratie.php">Inschrijving</a></button>
            <button><a href="../schema.php">Schema</a></button>
            <button><a href="../beheer_spelers.php">Deelnemers</a></button>
            <button><a href="login.php">Admin</a></button>
        </nav>
    </header>

    <div class="container">
        <div class="form-wrapper" style="max-width: 400px;">
            <h1>Admin Login</h1>
            
            <?php if (!empty($error)): ?>
                <div class="message message-error">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Gebruikersnaam</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-submit">Inloggen</button>
            </form>
            
            <div class="info-box">
                <p><strong>Demo Account:</strong></p>
                <p>Gebruikersnaam: <code>admin</code></p>
                <p>Wachtwoord: <code>admin123</code></p>
            </div>
            
            <div class="back-link">
                <a href="../../index.php">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>
